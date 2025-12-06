#!/bin/bash
# 本番環境切り替えスクリプト（改善版 - 互換性レイヤー）
# 
# 注意: このスクリプトは廃止予定です
# 新しい統合スクリプト switch-environment.sh を使用してください
#
# 使用例:
#   ./switch-environment.sh --to-production

echo "=========================================="
echo "注意: このスクリプトは廃止予定です"
echo "新しいスクリプトに転送しています..."
echo "=========================================="
echo ""

# スクリプトのディレクトリを取得
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# 新しいスクリプトを実行
exec "${SCRIPT_DIR}/switch-environment.sh" --to-production "$@"

# 以下は互換性のため残されていますが、実行されません
exit 0

set -e  # エラーで停止
set -u  # 未定義変数でエラー

# 設定
PRODUCTION_URL="https://low2023.com"
LOCAL_URL="http://localhost:8000"
BACKUP_DIR="./backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
DB_BACKUP_FILE="${BACKUP_DIR}/db_before_production_${TIMESTAMP}.sql"

# 色付き出力用
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# バックアップディレクトリの作成
mkdir -p "${BACKUP_DIR}"

echo "=========================================="
echo "本番環境への切り替え"
echo "=========================================="
echo ""
echo "ローカルURL: ${LOCAL_URL}"
echo "本番URL: ${PRODUCTION_URL}"
echo ""

# 確認プロンプト
echo -e "${RED}警告: この操作は本番環境に影響します！${NC}"
read -p "ローカル環境から本番環境に切り替えますか？ (y/N): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "キャンセルされました。"
    exit 1
fi

echo ""
echo "=========================================="
echo "ステップ1: データベースのバックアップ"
echo "=========================================="

if docker exec my-wp-site-db-1 mysqldump -u user -ppassword wordpress > "${DB_BACKUP_FILE}" 2>/dev/null; then
    echo -e "${GREEN}✓ バックアップ完了: ${DB_BACKUP_FILE}${NC}"
    BACKUP_SIZE=$(du -h "${DB_BACKUP_FILE}" | cut -f1)
    echo "  ファイルサイズ: ${BACKUP_SIZE}"
else
    echo -e "${RED}✗ バックアップに失敗しました${NC}"
    exit 1
fi

echo ""
echo "=========================================="
echo "ステップ2: コンテナの状態確認"
echo "=========================================="

# WordPressコンテナが起動しているか確認
if docker ps | grep -q "my-wp-site-wordpress-1"; then
    echo -e "${GREEN}✓ WordPressコンテナが起動しています${NC}"
else
    echo -e "${RED}✗ WordPressコンテナが起動していません${NC}"
    echo "  docker-compose up -d を実行してください"
    exit 1
fi

# WP-CLIが利用可能か確認
if docker exec my-wp-site-wordpress-1 wp --info --allow-root > /dev/null 2>&1; then
    echo -e "${GREEN}✓ WP-CLIが利用可能です${NC}"
else
    echo -e "${YELLOW}⚠ WP-CLIが利用できません。基本的な置換のみ実行します${NC}"
    WP_CLI_AVAILABLE=false
fi

echo ""
echo "=========================================="
echo "ステップ3: データベース内のURL置換"
echo "=========================================="

if [ "${WP_CLI_AVAILABLE:-true}" = true ]; then
    echo "WP-CLIを使用して完全な置換を実行中..."
    echo ""
    
    # ドライランで置換対象を確認
    echo "置換対象の確認中..."
    REPLACEMENTS=$(docker exec my-wp-site-wordpress-1 wp search-replace \
        "${LOCAL_URL}" "${PRODUCTION_URL}" \
        --all-tables \
        --dry-run \
        --allow-root 2>/dev/null | grep -oP '\d+(?= replacements)' | head -1 || echo "0")
    
    if [ "${REPLACEMENTS}" -gt 0 ]; then
        echo -e "${YELLOW}  ${REPLACEMENTS}件の置換が見つかりました${NC}"
        echo ""
        
        # 実際の置換を実行
        echo "置換を実行中..."
        if docker exec my-wp-site-wordpress-1 wp search-replace \
            "${LOCAL_URL}" "${PRODUCTION_URL}" \
            --all-tables \
            --allow-root 2>/dev/null > /dev/null; then
            echo -e "${GREEN}✓ ${REPLACEMENTS}件のURLを置換しました${NC}"
        else
            echo -e "${RED}✗ 置換に失敗しました${NC}"
            exit 1
        fi
    else
        echo -e "${GREEN}✓ 置換の必要はありません（すでに本番URLです）${NC}"
    fi
else
    # WP-CLIが使えない場合は直接SQLで更新
    echo "基本的なURL置換を実行中..."
    docker exec my-wp-site-db-1 mysql -u user -ppassword wordpress -e \
        "UPDATE wp_options SET option_value='${PRODUCTION_URL}' WHERE option_name IN ('siteurl', 'home');" 2>/dev/null
    echo -e "${GREEN}✓ サイトURLを更新しました${NC}"
    echo -e "${YELLOW}⚠ 投稿内容のURLは手動で確認してください${NC}"
fi

echo ""
echo "=========================================="
echo "ステップ4: キャッシュのクリア"
echo "=========================================="

# WordPressのキャッシュをクリア
if [ "${WP_CLI_AVAILABLE:-true}" = true ]; then
    if docker exec my-wp-site-wordpress-1 wp cache flush --allow-root 2>/dev/null; then
        echo -e "${GREEN}✓ WordPressキャッシュをクリアしました${NC}"
    else
        echo -e "${YELLOW}⚠ キャッシュクリアをスキップしました${NC}"
    fi
fi

echo ""
echo "=========================================="
echo "ステップ5: 設定の確認"
echo "=========================================="

echo "現在のサイトURL設定:"
docker exec my-wp-site-db-1 mysql -u user -ppassword wordpress -e \
    "SELECT option_name AS '設定項目', option_value AS 'URL' FROM wp_options WHERE option_name IN ('siteurl', 'home');" \
    2>/dev/null | grep -v "Warning" || true

echo ""
echo "=========================================="
echo -e "${GREEN}✓ 完了しました！${NC}"
echo "=========================================="
echo ""
echo "【次のステップ】"
echo "  1. wp-contentフォルダを本番サーバーにアップロード"
echo "  2. データベースを本番サーバーにエクスポート/インポート"
echo "  3. 本番環境でキャッシュをクリア"
echo "  4. ${PRODUCTION_URL} で動作確認"
echo ""
echo "【バックアップ】"
echo "  データベース: ${DB_BACKUP_FILE}"
echo ""
echo "【データベースのエクスポート】"
echo "  docker exec my-wp-site-db-1 mysqldump -u user -ppassword wordpress > production_ready_${TIMESTAMP}.sql"
echo ""
echo "【注意事項】"
echo "  - 本番環境に反映する前に、必ず本番環境のバックアップを取ってください"
echo "  - プラグインやテーマのバージョンが本番環境と一致しているか確認してください"
echo ""
echo "【トラブルシューティング】"
echo "  - 問題が発生した場合は、以下のコマンドでバックアップから復元できます:"
echo "    docker exec -i my-wp-site-db-1 mysql -u user -ppassword wordpress < ${DB_BACKUP_FILE}"
echo ""
echo "=========================================="
