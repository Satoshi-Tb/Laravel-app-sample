# composer だけ別ステージで拾う
FROM composer:2 AS composer_src

FROM php:8.4-fpm
WORKDIR /var/www/html

# bash を使う（nvm 用）
SHELL ["/bin/bash", "-c"]

# 非対話 & 失敗時即エラー
ENV DEBIAN_FRONTEND=noninteractive

RUN apt update; \
    apt install git zip unzip curl -y; \
    # PHP 拡張
    docker-php-ext-install pdo pdo_mysql mysqli; \
    docker-php-ext-enable pdo_mysql; \
    \
    # キャッシュ掃除
    rm -rf /var/lib/apt/lists/*

# composer を配置
COPY --from=composer_src /usr/bin/composer /usr/local/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# 公式スクリプトで nvm を導入し、LTS をインストール
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.1/install.sh | bash && \
    source ~/.bashrc && nvm install node --lts

# Laravel の書き込みディレクトリに www-data の権限を付与
RUN mkdir -p storage bootstrap/cache database && \
    touch database/database.sqlite && \
    chown -R www-data:www-data storage bootstrap/cache database
