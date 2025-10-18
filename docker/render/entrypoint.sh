#!/bin/sh
set -e

# Laravel プロジェクトのルートディレクトリへ移動
cd /var/www/html

# Artisan コマンドが利用可能なら設定・ルートをキャッシュ
if [ -f artisan ]; then
  # APP_KEYが設定されている場合、内容をキャッシュ
  if [ -n "${APP_KEY:-}" ]; then
    php artisan config:cache --quiet || true
    php artisan route:cache --quiet || true
  else
    echo "APP_KEY is empty; skip config and route cache." >&2
  fi
fi

# SQLite ファイルの作成
touch database/database.sqlite

# コンテナ起動ごとにマイグレーションを適用（Render Free プランで初期化されることを想定）
if [ -f artisan ]; then
  php artisan migrate --force
fi

# PHP-FPM が書き込むディレクトリの所有者を調整
chown -R www-data:www-data storage bootstrap/cache database

# PHP-FPM をバックグラウンド起動（ポート 9000 で待ち受け）
php-fpm -D

# Nginx をフォアグラウンドで起動してコンテナの PID 1 にする
exec nginx -g 'daemon off;'
