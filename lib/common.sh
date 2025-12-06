#!/bin/bash
# 共通関数ライブラリ
# WordPress環境切り替えスクリプトで使用する共通関数

# ==========================================
# 色定義
# ==========================================
export RED='\033[0;31m'
export GREEN='\033[0;32m'
export YELLOW='\033[1;33m'
export BLUE='\033[0;34m'
export CYAN='\033[0;36m'
export NC='\033[0m' # No Color

# ==========================================
# ログ出力関数
# ==========================================

log_info() {
    echo -e "${BLUE}ℹ${NC} $1"
}

log_success() {
    echo -e "${GREEN}✓${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}⚠${NC} $1"
}

log_error() {
    echo -e "${RED}✗${NC} $1" >&2
}

log_step() {
    echo ""
    echo "=========================================="
    echo "$1"
    echo "=========================================="
}

# ==========================================
# 環境変数読み込み
# ==========================================

load_env() {
    local env_file="${1:-.env}"
    
    if [ ! -f "$env_file" ]; then
        log_error "環境設定ファイルが見つかりません: $env_file"
        log_info ".env.example を .env にコピーして設定してください"
        log_info "  cp .env.example .env"
        return 1
    fi
    
    # .envファイルを読み込み
    set -a
    source "$env_file"
    set +a
    
    log_success "環境設定を読み込みました: $env_file"
    return 0
}

# ==========================================
# 前提条件チェック
# ==========================================

check_prerequisites() {
    local errors=0
    
    log_step "前提条件の確認"
    
    # Dockerがインストールされているか
    if ! command -v docker &> /dev/null; then
        log_error "Dockerがインストールされていません"
        ((errors++))
    else
        log_success "Docker: $(docker --version | cut -d' ' -f3 | cut -d',' -f1)"
    fi
    
    # Docker Composeがインストールされているか
    if ! command -v docker-compose &> /dev/null && ! docker compose version &> /dev/null; then
        log_error "Docker Composeがインストールされていません"
        ((errors++))
    else
        log_success "Docker Compose: 利用可能"
    fi
    
    return $errors
}

# ==========================================
# コンテナ状態チェック
# ==========================================

check_containers() {
    local wp_container="${WP_CONTAINER_NAME}"
    local db_container="${DB_CONTAINER_NAME}"
    local errors=0
    
    log_step "コンテナの状態確認"
    
    # WordPressコンテナ
    if docker ps --format '{{.Names}}' | grep -q "^${wp_container}$"; then
        log_success "WordPressコンテナが起動しています: ${wp_container}"
    else
        log_error "WordPressコンテナが起動していません: ${wp_container}"
        log_info "docker-compose up -d を実行してください"
        ((errors++))
    fi
    
    # データベースコンテナ
    if docker ps --format '{{.Names}}' | grep -q "^${db_container}$"; then
        log_success "データベースコンテナが起動しています: ${db_container}"
    else
        log_error "データベースコンテナが起動していません: ${db_container}"
        log_info "docker-compose up -d を実行してください"
        ((errors++))
    fi
    
    return $errors
}

# ==========================================
# WP-CLI利用可能性チェック
# ==========================================

check_wp_cli() {
    if docker exec "${WP_CONTAINER_NAME}" wp --info --allow-root > /dev/null 2>&1; then
        log_success "WP-CLIが利用可能です"
        return 0
    else
        log_warning "WP-CLIが利用できません。基本的な置換のみ実行します"
        return 1
    fi
}

# ==========================================
# バックアップ作成
# ==========================================

create_backup() {
    local backup_type="$1"
    local timestamp=$(date +%Y%m%d_%H%M%S)
    local backup_file="${BACKUP_DIR}/db_before_${backup_type}_${timestamp}.sql"
    
    log_step "データベースのバックアップ"
    
    # バックアップディレクトリの作成
    mkdir -p "${BACKUP_DIR}"
    
    # mysqldumpを実行
    if docker exec "${DB_CONTAINER_NAME}" mysqldump \
        -u "${DB_USER}" \
        -p"${DB_PASSWORD}" \
        "${DB_NAME}" > "$backup_file" 2>/dev/null; then
        
        local backup_size=$(du -h "$backup_file" | cut -f1)
        log_success "バックアップ完了: $backup_file"
        log_info "ファイルサイズ: ${backup_size}"
        
        # バックアップファイルパスをエクスポート（後で使用できるように）
        export LAST_BACKUP_FILE="$backup_file"
        return 0
    else
        log_error "バックアップに失敗しました"
        return 1
    fi
}

# ==========================================
# URL置換（WP-CLI使用）
# ==========================================

replace_urls_with_wpcli() {
    local from_url="$1"
    local to_url="$2"
    
    log_step "データベース内のURL置換"
    log_info "WP-CLIを使用して完全な置換を実行中..."
    echo ""
    
    # ドライランで置換対象を確認
    log_info "置換対象の確認中..."
    local replacements=$(docker exec "${WP_CONTAINER_NAME}" wp search-replace \
        "$from_url" "$to_url" \
        --all-tables \
        --dry-run \
        --allow-root 2>/dev/null | grep -oP '\d+(?= replacements)' | head -1 || echo "0")
    
    if [ "$replacements" -gt 0 ]; then
        echo -e "${YELLOW}  ${replacements}件の置換が見つかりました${NC}"
        echo ""
        
        # ドライランモードの場合はここで終了
        if [ "${DRY_RUN:-no}" = "yes" ]; then
            log_warning "[ドライラン] 実際の置換はスキップされました"
            return 0
        fi
        
        # 実際の置換を実行
        log_info "置換を実行中..."
        if docker exec "${WP_CONTAINER_NAME}" wp search-replace \
            "$from_url" "$to_url" \
            --all-tables \
            --allow-root 2>/dev/null > /dev/null; then
            log_success "${replacements}件のURLを置換しました"
            return 0
        else
            log_error "置換に失敗しました"
            return 1
        fi
    else
        log_success "置換の必要はありません（すでに${to_url}です）"
        return 0
    fi
}

# ==========================================
# URL置換（SQL直接実行）
# ==========================================

replace_urls_with_sql() {
    local from_url="$1"
    local to_url="$2"
    
    log_step "データベース内のURL置換"
    log_info "基本的なURL置換を実行中..."
    
    # ドライランモードの場合
    if [ "${DRY_RUN:-no}" = "yes" ]; then
        log_warning "[ドライラン] 実際の置換はスキップされました"
        return 0
    fi
    
    if docker exec "${DB_CONTAINER_NAME}" mysql \
        -u "${DB_USER}" \
        -p"${DB_PASSWORD}" \
        "${DB_NAME}" \
        -e "UPDATE wp_options SET option_value='${to_url}' WHERE option_name IN ('siteurl', 'home');" \
        2>/dev/null; then
        log_success "サイトURLを更新しました"
        log_warning "投稿内容のURLは手動で確認してください"
        return 0
    else
        log_error "URL更新に失敗しました"
        return 1
    fi
}

# ==========================================
# キャッシュクリア
# ==========================================

clear_cache() {
    log_step "キャッシュのクリア"
    
    # ドライランモードの場合
    if [ "${DRY_RUN:-no}" = "yes" ]; then
        log_warning "[ドライラン] キャッシュクリアはスキップされました"
        return 0
    fi
    
    # WordPressキャッシュ
    if check_wp_cli; then
        if docker exec "${WP_CONTAINER_NAME}" wp cache flush --allow-root 2>/dev/null; then
            log_success "WordPressキャッシュをクリアしました"
        else
            log_warning "キャッシュクリアをスキップしました"
        fi
    fi
    
    # Apache再起動
    log_info "Apacheを再起動中..."
    if docker exec "${WP_CONTAINER_NAME}" apache2ctl graceful 2>/dev/null; then
        log_success "Apacheを再起動しました"
    else
        log_warning "Apache再起動をスキップしました"
    fi
    
    return 0
}

# ==========================================
# 設定確認
# ==========================================

verify_settings() {
    log_step "設定の確認"
    
    echo "現在のサイトURL設定:"
    docker exec "${DB_CONTAINER_NAME}" mysql \
        -u "${DB_USER}" \
        -p"${DB_PASSWORD}" \
        "${DB_NAME}" \
        -e "SELECT option_name AS '設定項目', option_value AS 'URL' FROM wp_options WHERE option_name IN ('siteurl', 'home');" \
        2>/dev/null | grep -v "Warning" || true
    
    return 0
}

# ==========================================
# 確認プロンプト
# ==========================================

confirm_action() {
    local message="$1"
    local default="${2:-N}"
    
    # AUTO_CONFIRMが有効な場合はスキップ
    if [ "${AUTO_CONFIRM:-no}" = "yes" ]; then
        log_info "自動確認モード: スキップしました"
        return 0
    fi
    
    read -p "$message (y/N): " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        return 0
    else
        return 1
    fi
}

# ==========================================
# 完了メッセージ
# ==========================================

show_completion_message() {
    local env_type="$1"
    local target_url="$2"
    
    echo ""
    log_step "完了しました！"
    echo ""
    
    if [ "$env_type" = "local" ]; then
        echo "【アクセス情報】"
        echo "  サイトURL: ${target_url}"
        echo "  管理画面: ${target_url}/wp-admin"
        echo ""
        echo "【注意事項】"
        echo "  - 画像が表示されない場合は、本番環境から画像をダウンロードしてください"
        echo "  - プラグインやテーマのキャッシュが残っている場合は、ブラウザのキャッシュをクリアしてください"
    else
        echo "【次のステップ】"
        echo "  1. wp-contentフォルダを本番サーバーにアップロード"
        echo "  2. データベースを本番サーバーにエクスポート/インポート"
        echo "  3. 本番環境でキャッシュをクリア"
        echo "  4. ${target_url} で動作確認"
        echo ""
        echo "【注意事項】"
        echo "  - 本番環境に反映する前に、必ず本番環境のバックアップを取ってください"
        echo "  - プラグインやテーマのバージョンが本番環境と一致しているか確認してください"
    fi
    
    if [ -n "${LAST_BACKUP_FILE:-}" ]; then
        echo ""
        echo "【バックアップ】"
        echo "  データベース: ${LAST_BACKUP_FILE}"
        echo ""
        echo "【トラブルシューティング】"
        echo "  問題が発生した場合は、以下のコマンドでバックアップから復元できます:"
        echo "    docker exec -i ${DB_CONTAINER_NAME} mysql -u ${DB_USER} -p${DB_PASSWORD} ${DB_NAME} < ${LAST_BACKUP_FILE}"
    fi
    
    echo ""
    echo "=========================================="
}
