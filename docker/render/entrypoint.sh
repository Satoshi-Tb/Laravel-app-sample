#!/bin/sh
set -e

cd /var/www/html

if [ -f artisan ]; then
  if [ -n "${APP_KEY:-}" ]; then
    php artisan config:cache --quiet || true
    php artisan route:cache --quiet || true
  else
    echo "APP_KEY is empty; skip config and route cache." >&2
  fi
fi

touch database/database.sqlite

if [ -f artisan ]; then
  php artisan migrate --force
fi

chown -R www-data:www-data storage bootstrap/cache database

php-fpm -D

exec nginx -g 'daemon off;'
