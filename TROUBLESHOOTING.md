# WordPress Docker環境 トラブルシューティングガイド

## 目次

- [localhost:8000接続エラーの解決](#localhost8000接続エラーの解決)
- [本番環境への復元手順](#本番環境への復元手順)
- [よくある問題と解決策](#よくある問題と解決策)

---

## localhost:8000接続エラーの解決

### 問題の症状

- `http://localhost:8000`にアクセスすると「無効な応答」エラーが発生
- ブラウザで接続できない状態

### 原因

WordPressのデータベース設定で、`siteurl`と`home`のオプションが本番環境のURL（`https://low2023.com`）に設定されていたため、ローカル環境でのアクセス時にWordPressが正しく応答できない状態でした。

### 診断手順

#### 1. Dockerコンテナの状態確認

```bash
docker ps -a
docker-compose ps
```

#### 2. WordPressコンテナのログ確認

```bash
docker-compose logs --tail=50 wordpress
```

#### 3. ポート8000のリスニング状態確認

```bash
netstat -ano | findstr :8000
```

#### 4. curlでの接続テスト

```bash
curl -v http://localhost:8000
```

#### 5. データベース設定の確認

```bash
docker exec my-wp-site-db-1 mysql -u user -ppassword wordpress -e "SELECT option_name, option_value FROM wp_options WHERE option_name IN ('siteurl', 'home');"
```

### 解決策

#### ローカル環境用にURL設定を変更

```bash
docker exec my-wp-site-db-1 mysql -u user -ppassword wordpress -e "UPDATE wp_options SET option_value='http://localhost:8000' WHERE option_name IN ('siteurl', 'home');"
```

#### 変更の確認

```bash
docker exec my-wp-site-db-1 mysql -u user -ppassword wordpress -e "SELECT option_name, option_value FROM wp_options WHERE option_name IN ('siteurl', 'home');"
```

#### 接続テスト

```bash
curl -I http://localhost:8000
```

正常な応答例:

```
HTTP/1.1 200 OK
Date: Wed, 26 Nov 2025 13:09:38 GMT
Content-Type: text/html; charset=UTF-8
```

---

## 本番環境への復元手順

### 注意事項
>
> [!WARNING]
> 本番環境に復元する前に、必ずローカルでの作業内容をバックアップしてください。

### 手順1: データベースのバックアップ

#### ローカル環境のデータベースをエクスポート

```bash
docker exec my-wp-site-db-1 mysqldump -u user -ppassword wordpress > backup_$(date +%Y%m%d_%H%M%S).sql
```

### 手順2: URL設定を本番環境に戻す

#### 方法A: SQLコマンドで直接変更（推奨）

```bash
docker exec my-wp-site-db-1 mysql -u user -ppassword wordpress -e "UPDATE wp_options SET option_value='https://low2023.com' WHERE option_name IN ('siteurl', 'home');"
```

#### 方法B: バックアップファイルを編集して復元

1. バックアップファイル内の`http://localhost:8000`を`https://low2023.com`に置換
2. 本番環境のデータベースにインポート

### 手順3: 設定の確認

```bash
docker exec my-wp-site-db-1 mysql -u user -ppassword wordpress -e "SELECT option_name, option_value FROM wp_options WHERE option_name IN ('siteurl', 'home');"
```

期待される出力:

```
option_name     option_value
siteurl         https://low2023.com
home            https://low2023.com
```

### 手順4: wp-contentのアップロード

#### ローカルのwp-contentを本番環境に同期

```bash
# 例: rsyncを使用する場合
rsync -avz --delete ./wp-content/ user@production-server:/path/to/wordpress/wp-content/
```

または、FTP/SFTPクライアントを使用してアップロード

### 手順5: .htaccessファイルの確認

本番環境の`.htaccess`ファイルが正しく設定されているか確認:

```apache
# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteBase /
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
</IfModule>
# END WordPress

# All-in-One WP Migration Settings
php_value upload_max_filesize 2G
php_value post_max_size 2G
php_value memory_limit 256M
php_value max_execution_time 6000
php_value max_input_time 6000
```

### 手順6: キャッシュのクリア

#### WordPressのキャッシュをクリア

- 管理画面にログイン
- 使用しているキャッシュプラグインでキャッシュをクリア
- ブラウザのキャッシュもクリア

### 手順7: 動作確認

#### チェックリスト

- [ ] `https://low2023.com`にアクセスできる
- [ ] トップページが正常に表示される
- [ ] 管理画面（`/wp-admin`）にログインできる
- [ ] 投稿・固定ページが正常に表示される
- [ ] 画像・メディアファイルが正常に表示される
- [ ] プラグインが正常に動作する
- [ ] SSL証明書が有効である

---

## よくある問題と解決策

### 問題1: 画像が表示されない

#### 原因

データベース内のメディアURLがローカル環境のままになっている

#### 解決策

```bash
# データベース内のURLを一括置換
docker exec my-wp-site-db-1 mysql -u user -ppassword wordpress -e "
UPDATE wp_posts SET post_content = REPLACE(post_content, 'http://localhost:8000', 'https://low2023.com');
UPDATE wp_posts SET guid = REPLACE(guid, 'http://localhost:8000', 'https://low2023.com');
UPDATE wp_postmeta SET meta_value = REPLACE(meta_value, 'http://localhost:8000', 'https://low2023.com');
"
```

### 問題2: リダイレクトループが発生する

#### 原因

- `.htaccess`の設定が不適切
- SSL設定が正しくない

#### 解決策

1. `.htaccess`を確認・修正
2. `wp-config.php`にSSL設定を追加:

```php
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}
```

### 問題3: パーマリンクが機能しない

#### 原因

- `.htaccess`が正しく設定されていない
- mod_rewriteが有効になっていない

#### 解決策

1. 管理画面の「設定」→「パーマリンク設定」で設定を保存し直す
2. `.htaccess`のパーミッションを確認（644推奨）

---

## 環境切り替えスクリプト

### ローカル環境用スクリプト（switch-to-local.sh）

```bash
#!/bin/bash
echo "ローカル環境に切り替えています..."
docker exec my-wp-site-db-1 mysql -u user -ppassword wordpress -e "UPDATE wp_options SET option_value='http://localhost:8000' WHERE option_name IN ('siteurl', 'home');"
echo "完了しました。http://localhost:8000 でアクセスできます。"
```

### 本番環境用スクリプト（switch-to-production.sh）

```bash
#!/bin/bash
echo "本番環境に切り替えています..."
docker exec my-wp-site-db-1 mysql -u user -ppassword wordpress -e "UPDATE wp_options SET option_value='https://low2023.com' WHERE option_name IN ('siteurl', 'home');"
echo "完了しました。データベースのバックアップと本番環境へのデプロイを忘れずに行ってください。"
```

### 使用方法

```bash
# Windows (PowerShell)
bash switch-to-local.sh
bash switch-to-production.sh

# または直接コマンドを実行
```

---

## 参考情報

### データベース接続情報

- **ホスト**: db（Docker内）/ localhost:3306（ホストから）
- **データベース名**: wordpress
- **ユーザー名**: user
- **パスワード**: password
- **ルートパスワード**: rootpassword

### 重要なファイルパス

- **docker-compose.yml**: `i:\Docker\my-wp-site\docker-compose.yml`
- **wp-content**: `i:\Docker\my-wp-site\wp-content`
- **.htaccess**: `i:\Docker\my-wp-site\.htaccess`
- **uploads.ini**: `i:\Docker\my-wp-site\uploads.ini`

### 関連コマンド

```bash
# コンテナの起動
docker-compose up -d

# コンテナの停止
docker-compose down

# コンテナの再起動
docker-compose restart

# ログの確認
docker-compose logs -f wordpress

# データベースに直接接続
docker exec -it my-wp-site-db-1 mysql -u user -ppassword wordpress
```

---

**最終更新日**: 2025-11-26  
**作成者**: Antigravity AI Assistant
