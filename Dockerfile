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

# バックエンド用ステージで PHP 実行環境を準備
FROM php:8.3-fpm-alpine AS backend
WORKDIR /var/www/html

# 実行時に必要なパッケージと PHP 拡張をインストール
RUN apk add --no-cache git curl unzip sqlite sqlite-dev
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

# Web ユーザーでプロセスを実行
USER www-data

# Laravel 開発サーバーを公開するポートを指定
EXPOSE 8000

# マイグレーション後に Laravel 組み込みサーバーを起動
CMD ["sh", "-c", "touch database/database.sqlite && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
