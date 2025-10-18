# syntax=docker/dockerfile:1

# フロントエンド資産をビルドするステージ
FROM node:20-alpine AS frontend
WORKDIR /app

# Vite ビルドに必要な JavaScript 依存関係を導入
COPY package.json package-lock.json ./
RUN npm ci

# ビルド対象の設定ファイルとフロントエンドソースをコピー
COPY vite.config.ts tsconfig.json ./
COPY resources ./resources
COPY public ./public

# 本番向けのアセットを生成
RUN npm run build

# バックエンド用ステージで PHP + Nginx 実行環境を準備
FROM php:8.3-fpm-alpine AS production
WORKDIR /var/www/html

# 実行時に必要なパッケージと PHP 拡張をインストール
RUN apk add --no-cache git curl unzip sqlite sqlite-dev nginx
RUN docker-php-ext-install pdo_sqlite

# Composer バイナリをコピー
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 開発依存を除いて PHP 依存関係を導入
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --ansi --no-scripts

# アプリケーションコードとビルド済みアセットを取り込み
COPY . ./
COPY --from=frontend /app/public/build ./public/build

# コンテナ起動を高速化するため各種キャッシュを初期化
RUN rm -f bootstrap/cache/*.php \
    && php artisan package:discover --ansi

# 実行ユーザーに必要なディレクトリ所有権を付与
RUN chown -R www-data:www-data storage bootstrap/cache database

# Nginx デフォルト設定とログ出力先を Render 向けに調整
RUN rm -f /etc/nginx/http.d/default.conf \
    && mkdir -p /run/nginx \
    && ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log

# Render 用に調整した Nginx / PHP-FPM / エントリポイントを配置
COPY docker/render/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/render/php-fpm/www.conf /usr/local/etc/php-fpm.d/zz-www.conf
COPY docker/render/entrypoint.sh /usr/local/bin/entrypoint.sh

# エントリポイントを実行可能にし、Render が期待するポートを公開
RUN chmod +x /usr/local/bin/entrypoint.sh
ENV PORT=8080
EXPOSE 8080

# マイグレーション後に PHP-FPM と Nginx を起動
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
