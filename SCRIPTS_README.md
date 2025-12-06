# 環境切り替えスクリプト使用ガイド

## 概要

このディレクトリには、WordPressサイトをローカル環境と本番環境の間で切り替えるための統合スクリプトが含まれています。

## 🎯 推奨: 新しい統合スクリプト

### `switch-environment.sh` - 統合環境切り替えスクリプト

**特徴:**

- ✅ 1つのスクリプトで双方向の切り替えに対応
- ✅ セキュリティ強化（環境変数でDB認証情報を管理）
- ✅ ドライラン機能
- ✅ 詳細なエラーハンドリング
- ✅ 自動バックアップ
- ✅ 完全なURL置換（投稿内容、画像URLも含む）
- ✅ キャッシュ自動クリア

## クイックスタート

### 1. 初期設定

```bash
# 環境設定ファイルを作成
cp .env.example .env

# 必要に応じて .env を編集（デフォルト値で動作します）
# nano .env
```

### 2. 実行権限の付与（初回のみ）

```bash
chmod +x switch-environment.sh
```

### 3. 環境の切り替え

#### ローカル環境への切り替え

```bash
./switch-environment.sh --to-local
```

#### 本番環境への切り替え

```bash
./switch-environment.sh --to-production
```

## 使用方法

### 基本的な使い方

```bash
# ローカル環境に切り替え
./switch-environment.sh --to-local

# 本番環境に切り替え
./switch-environment.sh --to-production

# 確認プロンプトをスキップ
./switch-environment.sh --to-local --yes

# ドライラン（実際には変更しない）
./switch-environment.sh --to-local --dry-run

# ヘルプを表示
./switch-environment.sh --help
```

### オプション一覧

| オプション | 説明 |
|-----------|------|
| `--to-local` | ローカル環境に切り替え |
| `--to-production` | 本番環境に切り替え |
| `--yes`, `-y` | 確認プロンプトをスキップ |
| `--dry-run` | 実際の変更を行わず、実行内容のみ表示 |
| `--env FILE` | 環境設定ファイルを指定（デフォルト: `.env`） |
| `--help`, `-h` | ヘルプを表示 |

### PowerShellから実行する場合

```powershell
# Git Bash経由で実行
bash switch-environment.sh --to-local
```

## 実行例

### ローカル環境への切り替え

```text
==========================================
ローカル環境への切り替え
==========================================

✓ 環境設定を読み込みました: .env

ℹ 切り替え元URL: https://low2023.com
ℹ 切り替え先URL: http://localhost:8000

ローカル環境に切り替えますか？ (y/N): y

==========================================
前提条件の確認
==========================================
✓ Docker: 24.0.7
✓ Docker Compose: 利用可能

==========================================
コンテナの状態確認
==========================================
✓ WordPressコンテナが起動しています: my-wp-site-wordpress-1
✓ データベースコンテナが起動しています: my-wp-site-db-1
✓ WP-CLIが利用可能です

==========================================
データベースのバックアップ
==========================================
✓ バックアップ完了: ./backups/db_before_local_20251130_064500.sql
ℹ ファイルサイズ: 15M

==========================================
データベース内のURL置換
==========================================
ℹ WP-CLIを使用して完全な置換を実行中...

ℹ 置換対象の確認中...
⚠   127件の置換が見つかりました

ℹ 置換を実行中...
✓ 127件のURLを置換しました

==========================================
キャッシュのクリア
==========================================
✓ WordPressキャッシュをクリアしました
ℹ Apacheを再起動中...
✓ Apacheを再起動しました

==========================================
設定の確認
==========================================
現在のサイトURL設定:
設定項目    URL
siteurl     http://localhost:8000
home        http://localhost:8000

==========================================
完了しました！
==========================================

【アクセス情報】
  サイトURL: http://localhost:8000
  管理画面: http://localhost:8000/wp-admin

【注意事項】
  - 画像が表示されない場合は、本番環境から画像をダウンロードしてください
  - プラグインやテーマのキャッシュが残っている場合は、ブラウザのキャッシュをクリアしてください

【バックアップ】
  データベース: ./backups/db_before_local_20251130_064500.sql

【トラブルシューティング】
  問題が発生した場合は、以下のコマンドでバックアップから復元できます:
    docker exec -i my-wp-site-db-1 mysql -u user -ppassword wordpress < ./backups/db_before_local_20251130_064500.sql

==========================================
```

## 環境設定ファイル (.env)

`.env.example`をコピーして`.env`を作成し、必要に応じて設定を変更できます。

```bash
# データベース設定
DB_CONTAINER_NAME=my-wp-site-db-1
DB_USER=user
DB_PASSWORD=password
DB_NAME=wordpress

# WordPress設定
WP_CONTAINER_NAME=my-wp-site-wordpress-1

# URL設定
PRODUCTION_URL=https://low2023.com
LOCAL_URL=http://localhost:8000

# バックアップ設定
BACKUP_DIR=./backups

# スクリプト動作設定
AUTO_CONFIRM=no      # 確認プロンプトをスキップ (yes/no)
VERBOSE=yes          # 詳細ログを出力 (yes/no)
DRY_RUN=no          # ドライランモード (yes/no)
```

## 旧スクリプトからの移行

### 互換性について

既存のスクリプトは引き続き使用できますが、新しい統合スクリプトへのラッパーとして機能します。

```bash
# これらのスクリプトは引き続き動作します
./switch-to-local.sh
./switch-to-local-improved.sh
./switch-to-production.sh
./switch-to-production-improved.sh
```

実行すると、自動的に新しいスクリプトに転送されます。

### 移行手順

1. **環境設定ファイルの作成**

   ```bash
   cp .env.example .env
   ```

2. **新しいスクリプトの使用開始**

   ```bash
   ./switch-environment.sh --to-local
   ```

3. **旧スクリプトの削除（オプション）**

   新しいスクリプトに慣れたら、旧スクリプトは削除できます：

   ```bash
   rm switch-to-local.sh
   rm switch-to-local-improved.sh
   rm switch-to-production.sh
   rm switch-to-production-improved.sh
   ```

## 新機能の詳細

### 🔒 セキュリティ強化

**変更前:**

```bash
docker exec my-wp-site-db-1 mysql -u user -ppassword wordpress
```

**変更後:**

```bash
# .envファイルから読み込み
docker exec ${DB_CONTAINER_NAME} mysql -u ${DB_USER} -p${DB_PASSWORD} ${DB_NAME}
```

- パスワードがコマンドラインに露出しない
- `.env`ファイルを`.gitignore`に追加して秘密情報を保護

### 🧪 ドライラン機能

実際の変更を行わずに、何が実行されるかを確認できます：

```bash
./switch-environment.sh --to-local --dry-run
```

### 📦 モジュール化

共通機能は`lib/common.sh`に分離され、再利用可能になりました：

- ログ出力関数
- バックアップ関数
- URL置換関数
- 検証関数

## トラブルシューティング

### 1. 環境設定ファイルが見つかりません

```bash
# エラー
✗ 環境設定ファイルが見つかりません: .env

# 解決方法
cp .env.example .env
```

### 2. Dockerコンテナが起動していない

```bash
# コンテナの状態を確認
docker ps

# コンテナを起動
docker-compose up -d
```

### 3. 画像が表示されない

データベース内のURLは正しく置換されますが、画像ファイル自体がローカル環境に存在しない場合は表示されません。

**解決方法:**

- 本番環境から`wp-content/uploads/`フォルダを同期

### 4. バックアップから復元する

```bash
# バックアップファイルを確認
ls -lh ./backups/

# 復元（例）
docker exec -i my-wp-site-db-1 mysql -u user -ppassword wordpress < ./backups/db_before_local_20251130_064500.sql
```

### 5. スクリプトが実行できない

```bash
# 実行権限を確認
ls -l switch-environment.sh

# 実行権限を付与
chmod +x switch-environment.sh
```

## ファイル構成

```
my-wp-site/
├── switch-environment.sh          # 統合スクリプト（推奨）
├── lib/
│   └── common.sh                  # 共通関数ライブラリ
├── .env.example                   # 環境設定テンプレート
├── .env                           # 実際の環境設定（gitignore）
├── SCRIPTS_README.md              # このファイル
├── backups/                       # バックアップディレクトリ
│   └── db_before_*.sql
├── switch-to-local.sh             # 旧スクリプト（互換性レイヤー）
├── switch-to-local-improved.sh    # 旧スクリプト（互換性レイヤー）
├── switch-to-production.sh        # 旧スクリプト（互換性レイヤー）
└── switch-to-production-improved.sh # 旧スクリプト（互換性レイヤー）
```

- `.htaccess` - Apache リライトルール
- `docker-compose.yml` - Docker環境設定
- `Dockerfile` - Dockerイメージ設定

## サポート

問題が発生した場合は、以下を確認してください：

1. ✅ Dockerコンテナが正常に起動しているか
2. ✅ データベースに接続できるか
3. ✅ WP-CLIが利用可能か
4. ✅ バックアップファイルが作成されているか
5. ✅ `.env`ファイルが正しく設定されているか

---

**最終更新:** 2025年11月
**バージョン:** 2.0（統合版）
