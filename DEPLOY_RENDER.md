# Render へのデプロイ & GitHub 連携 CI/CD 手順

## 前提

- Render アカウントと GitHub アカウントを用意し、当リポジトリを GitHub 上に配置しておく。
- Dockerfile をリポジトリ直下へ配置してある想定（未作成の場合は下記を参照）。
- SQLite を永続化する場合は Render の Persistent Disk（無料枠 1GB）を利用する。

## 1. Dockerfile の準備

Laravel と Vite をビルドし、`php artisan serve` でポート 8000 を公開するシンプルな例:

```dockerfile
FROM node:20-alpine AS frontend
WORKDIR /app
COPY package*.json vite.config.* /app/
RUN npm install
COPY resources ./resources
RUN npm run build

FROM php:8.3-fpm-alpine AS backend
WORKDIR /var/www/html
RUN apk add --no-cache git curl unzip sqlite
RUN docker-php-ext-install pdo pdo_sqlite
COPY composer.json composer.lock ./
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader
COPY . ./
COPY --from=frontend /app/public/build ./public/build
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
```

## 2. Render サービスの作成

1. Render ダッシュボードで **New → Web Service** を選択し、GitHub リポジトリを接続。
2. Branch はデプロイ対象ブランチを選択、Environment は **Docker** を選択。
3. Build Command は空欄のままで OK（Dockerfile がそのまま使われる）。
4. Advanced→Disk で Persistent Disk を追加する場合はパスと容量を指定（例: `/var/www/html/storage` で 1GB）。
5. 環境変数に `.env` と同等の値を設定。
   - `APP_KEY` はローカルで `php artisan key:generate --show` を実行して値をコピー。
   - `APP_ENV=production`、`APP_DEBUG=false`、`APP_URL=https://<service-name>.onrender.com` なども設定。
6. **Create Web Service** をクリックすると初回デプロイが実行される。

## 3. SQLite の永続化（任意）

- Persistent Disk を `/var/www/html/database` などにマウントし、`config/database.php` の SQLite パスをそのディスク上へ変更する。
- Render のコンソールで `php artisan migrate` を実行して初期テーブルを作成。

## 4. GitHub 連携で自動デプロイ

Render は GitHub の対象ブランチへ push されるたび自動で再デプロイします。CI/CD をより厳密にしたい場合は以下のようにする。

1. GitHub Actions でテストを走らせ、成功時のみ Render のデプロイ API を叩くワークフローを作成。
2. Render ダッシュボードで **Manual Deploys** を「Allow builds only after successful checks」に設定すると、GitHub Actions の Status API と連携できます。

GitHub Actions の例（`.github/workflows/deploy.yml`）:

```yaml
name: Test and Deploy

on:
  push:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
      - run: composer install --no-progress --prefer-dist
      - run: cp .env.example .env
      - run: php artisan key:generate
      - run: php artisan test

  deploy:
    needs: test
    runs-on: ubuntu-latest
    steps:
      - name: Trigger Render deploy
        uses: render-oss/render-action@v1
        with:
          service-id: ${{ secrets.RENDER_SERVICE_ID }}
          api-key: ${{ secrets.RENDER_API_KEY }}
```

- `service-id` と `api-key` は Render ダッシュボードから取得し、GitHub リポジトリの Secrets に保存する。

## 5. 動作確認

1. Render デプロイログで `Service live` になるまで待つ。
2. HTTPS URL にアクセスして表示確認。
3. 管理画面で「Manual Deploy」を試して GitHub Actions のステータス連携が動いているか確認する。

以上で Render へ Laravel アプリをデプロイし、GitHub ベースの CI/CD を構築できます。デモ用途で無料枠を使いたい場合は、サービスがスリープする点に注意しておいてください。
