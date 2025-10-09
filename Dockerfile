# syntax=docker/dockerfile:1

FROM node:20-alpine AS frontend
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.ts tsconfig.json ./
COPY resources ./resources
COPY public ./public

RUN npm run build

FROM php:8.3-fpm-alpine AS backend
WORKDIR /var/www/html

RUN apk add --no-cache git curl unzip sqlite sqlite-dev
RUN docker-php-ext-install pdo_sqlite

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --ansi --no-scripts

COPY . ./
COPY --from=frontend /app/public/build ./public/build

RUN rm -f bootstrap/cache/*.php \
    && php artisan package:discover --ansi

RUN chown -R www-data:www-data storage bootstrap/cache database

USER www-data

EXPOSE 8000

CMD ["sh", "-c", "touch database/database.sqlite && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
