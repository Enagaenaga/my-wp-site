# ベースイメージとして公式のWordPressイメージを使用
FROM wordpress:latest

# mod_rewriteを有効化
RUN a2enmod rewrite

# AllowOverride Allの設定を確認
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# 作業ディレクトリを設定
WORKDIR /var/www/html

# WP-CLIのインストール
RUN curl -O https://raw.githubusercontent.com/wp-cli/builds/gh-pages/phar/wp-cli.phar \
    && chmod +x wp-cli.phar \
    && mv wp-cli.phar /usr/local/bin/wp

# ローカルのwp-contentフォルダをコンテナにコピー
COPY wp-content /var/www/html/wp-content
