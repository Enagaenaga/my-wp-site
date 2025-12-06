#!/bin/bash
# WordPress環境切り替えスクリプト（統合版）
# ローカル環境と本番環境の双方向切り替えに対応

set -e  # エラーで停止
set -u  # 未定義変数でエラー

# ==========================================
# スクリプトのディレクトリを取得
# ==========================================
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

# ==========================================
# 共通関数ライブラリの読み込み
# ==========================================
if [ -f "${SCRIPT_DIR}/lib/common.sh" ]; then
    source "${SCRIPT_DIR}/lib/common.sh"
else
    echo "エラー: 共通関数ライブラリが見つかりません: ${SCRIPT_DIR}/lib/common.sh"
    exit 1
fi

# ==========================================
# 使用方法の表示
# ==========================================
show_usage() {
    cat << EOF
使用方法: $0 [オプション]

WordPress環境をローカルと本番の間で切り替えます。

オプション:
  --to-local          ローカル環境に切り替え
  --to-production     本番環境に切り替え
  --yes, -y           確認プロンプトをスキップ
  --dry-run           実際の変更を行わず、実行内容のみ表示
  --env FILE          環境設定ファイルを指定（デフォルト: .env）
  --help, -h          このヘルプを表示

使用例:
  # ローカル環境に切り替え
  $0 --to-local

  # 本番環境に切り替え（確認なし）
  $0 --to-production --yes

  # ドライラン（実際には変更しない）
  $0 --to-local --dry-run

  # カスタム環境設定ファイルを使用
  $0 --to-local --env .env.staging

注意:
  - 初回実行時は .env.example を .env にコピーして設定してください
  - 本番環境への切り替え前に、必ずバックアップを確認してください

EOF
}

# ==========================================
# 引数解析
# ==========================================
DIRECTION=""
ENV_FILE=".env"

while [[ $# -gt 0 ]]; do
    case $1 in
        --to-local)
            DIRECTION="local"
            shift
            ;;
        --to-production)
            DIRECTION="production"
            shift
            ;;
        --yes|-y)
            export AUTO_CONFIRM="yes"
            shift
            ;;
        --dry-run)
            export DRY_RUN="yes"
            shift
            ;;
        --env)
            ENV_FILE="$2"
            shift 2
            ;;
        --help|-h)
            show_usage
            exit 0
            ;;
        *)
            log_error "不明なオプション: $1"
            echo ""
            show_usage
            exit 1
            ;;
    esac
done

# ==========================================
# 方向が指定されていない場合はエラー
# ==========================================
if [ -z "$DIRECTION" ]; then
    log_error "切り替え方向を指定してください"
    echo ""
    show_usage
    exit 1
fi

# ==========================================
# メイン処理
# ==========================================
main() {
    # ヘッダー表示
    if [ "$DIRECTION" = "local" ]; then
        log_step "ローカル環境への切り替え"
    else
        log_step "本番環境への切り替え"
    fi
    
    # ドライランモードの表示
    if [ "${DRY_RUN:-no}" = "yes" ]; then
        echo ""
        log_warning "【ドライランモード】実際の変更は行われません"
    fi
    
    echo ""
    
    # 環境変数の読み込み
    if ! load_env "$ENV_FILE"; then
        exit 1
    fi
    
    # URLの設定
    if [ "$DIRECTION" = "local" ]; then
        FROM_URL="${PRODUCTION_URL}"
        TO_URL="${LOCAL_URL}"
        BACKUP_TYPE="local"
    else
        FROM_URL="${LOCAL_URL}"
        TO_URL="${PRODUCTION_URL}"
        BACKUP_TYPE="production"
    fi
    
    echo ""
    log_info "切り替え元URL: ${FROM_URL}"
    log_info "切り替え先URL: ${TO_URL}"
    echo ""
    
    # 本番環境への切り替えの場合は警告
    if [ "$DIRECTION" = "production" ]; then
        echo -e "${RED}警告: この操作は本番環境に影響します！${NC}"
        echo ""
    fi
    
    # 確認プロンプト
    if ! confirm_action "${DIRECTION}環境に切り替えますか？"; then
        log_info "キャンセルされました。"
        exit 0
    fi
    
    # 前提条件のチェック
    if ! check_prerequisites; then
        log_error "前提条件を満たしていません"
        exit 1
    fi
    
    # コンテナの状態確認
    if ! check_containers; then
        log_error "コンテナが正しく起動していません"
        exit 1
    fi
    
    # WP-CLIの確認
    WP_CLI_AVAILABLE=false
    if check_wp_cli; then
        WP_CLI_AVAILABLE=true
    fi
    
    # バックアップの作成
    if ! create_backup "$BACKUP_TYPE"; then
        log_error "バックアップに失敗しました"
        exit 1
    fi
    
    # URL置換
    if [ "$WP_CLI_AVAILABLE" = true ]; then
        if ! replace_urls_with_wpcli "$FROM_URL" "$TO_URL"; then
            log_error "URL置換に失敗しました"
            exit 1
        fi
    else
        if ! replace_urls_with_sql "$FROM_URL" "$TO_URL"; then
            log_error "URL置換に失敗しました"
            exit 1
        fi
    fi
    
    # キャッシュのクリア
    clear_cache
    
    # 設定の確認
    verify_settings
    
    # 完了メッセージ
    show_completion_message "$DIRECTION" "$TO_URL"
}

# ==========================================
# エラーハンドラー
# ==========================================
error_handler() {
    local line_number=$1
    echo ""
    log_error "エラーが発生しました（行: ${line_number}）"
    
    if [ -n "${LAST_BACKUP_FILE:-}" ]; then
        echo ""
        log_info "バックアップから復元するには、以下のコマンドを実行してください:"
        echo "  docker exec -i ${DB_CONTAINER_NAME} mysql -u ${DB_USER} -p${DB_PASSWORD} ${DB_NAME} < ${LAST_BACKUP_FILE}"
    fi
    
    exit 1
}

trap 'error_handler ${LINENO}' ERR

# ==========================================
# スクリプト実行
# ==========================================
main
