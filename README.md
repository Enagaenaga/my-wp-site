# WordPress Docker開発環境

このディレクトリには、WordPressのローカル開発環境が含まれています。

## 📁 ディレクトリ構成

```
my-wp-site/
├── docker-compose.yml          # Docker Compose設定ファイル
├── .htaccess                   # Apache設定ファイル
├── uploads.ini                 # PHPアップロード設定
├── wp-content/                 # WordPressコンテンツディレクトリ
├── TROUBLESHOOTING.md          # トラブルシューティングガイド
├── switch-to-local.sh          # ローカル環境切り替えスクリプト
├── switch-to-production.sh     # 本番環境切り替えスクリプト
└── README.md                   # このファイル
```

## 🚀 クイックスタート

### 環境の起動

```bash
docker-compose up -d
```

### 環境の停止

```bash
docker-compose down
```

### ローカル環境でのアクセス

```
http://localhost:8080
```

## 🔧 環境切り替え

### ローカル環境に切り替え

```bash
bash switch-to-local.sh
```

### 本番環境に切り替え（デプロイ前）

```bash
bash switch-to-production.sh
```

## 📊 データベース情報

- **ホスト**: db（Docker内）
- **ポート**: 3306
- **データベース名**: wordpress
- **ユーザー名**: user
- **パスワード**: password
- **ルートパスワード**: rootpassword

## 🔍 トラブルシューティング

問題が発生した場合は、[TROUBLESHOOTING.md](./TROUBLESHOOTING.md)を参照してください。

### よくある問題

#### localhost:8080に接続できない

```bash
# データベースのURL設定を確認
docker exec my-wp-site-db-1 mysql -u user -ppassword wordpress -e "SELECT option_name, option_value FROM wp_options WHERE option_name IN ('siteurl', 'home');"

# ローカル環境に切り替え
bash switch-to-local.sh
```

#### コンテナの状態確認

```bash
docker-compose ps
docker-compose logs -f wordpress
```

## 📝 重要な注意事項

> [!WARNING]
>
> - 本番環境へのデプロイ前に必ずバックアップを取得してください
> - ローカル環境と本番環境でURL設定が異なるため、環境切り替えスクリプトを使用してください
> - データベースのパスワードは本番環境では必ず変更してください

## 🔗 関連リンク

- [WordPress公式ドキュメント](https://ja.wordpress.org/support/)
- [Docker公式ドキュメント](https://docs.docker.com/)
- [本番環境](https://low2023.com)

## 📞 サポート

問題が解決しない場合は、TROUBLESHOOTING.mdの詳細な手順を確認してください。
