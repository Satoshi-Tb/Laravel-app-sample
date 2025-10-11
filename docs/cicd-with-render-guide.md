# Render へのデプロイ & GitHub 連携 CI/CD 手順（Free プラン想定）

## 前提

- Render アカウントと GitHub アカウントを用意し、当リポジトリを GitHub 上に配置しておく。
- Dockerfile をリポジトリ直下へ配置してある想定（未作成の場合は下記を参照）。
- Free Web Service を利用するため、SQLite データベースはデプロイやスリープ復帰のたびに初期化される前提で構わないこと。

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

### ローカルでの動作確認

Dockerfile を用意したら、Render へ push する前にローカルでビルドと起動確認を行う:

```bash
docker build -t laravel-app:dev .
docker run --rm -d --name laravel-app -p 8000:8000 --env-file .env laravel-app:dev
```

`http://localhost:8000` にアクセスしてアプリが期待通り動くか確認する。`docker stop laravel-app` で停止すると `--rm` によりコンテナが自動削除されるので、続けて `docker image rm laravel-app:dev` でローカルイメージも整理しておくとよい。

## 2. Render サービスの作成

1. Render ダッシュボードで **New → Web Service** を選択し、GitHub リポジトリを接続。
2. Branch はデプロイ対象ブランチを選択、Environment は **Docker** を選択。
3. Build Command は空欄のままで OK（Dockerfile がそのまま使われる）。
4. Free Web Service では Persistent Disk を追加できない点に注意（Starter 以上で利用可能）。
5. 環境変数に `.env` と同等の値を設定。
   - `APP_KEY` はローカルで `php artisan key:generate --show` を実行して値をコピー。
   - `APP_ENV=production`、`APP_DEBUG=false`、`APP_URL=https://<service-name>.onrender.com` なども設定。
6. **Create Web Service** をクリックすると初回デプロイが実行される。

## 3. SQLite 運用（Free プラン）

- Free Web Service は再デプロイやスリープ復帰のたびにファイルシステムがリセットされるため、SQLite データは毎回初期化される前提で運用する。
- Laravel 側では `config/database.php` の SQLite 設定をそのまま利用し、`database/database.sqlite` を使う（コンテナ起動時に `touch database/database.sqlite` しておくとファイル未作成エラーを避けられる）。
- 起動時に自動でテーブルを作成するため、デプロイコマンドに `php artisan migrate --force`（必要なら `--seed`）を追加するか、GitHub Actions のワークフローに同コマンドを組み込む。
- 本番同等の永続化が必要になったら、Render External Database（MySQL/PostgreSQL）や他社の無料 DB サービスを利用し、`.env` 相当の環境変数で接続設定を渡す。

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
- Free プランでの SQLite 初期化対応として、Render サービスの Start Command を `php artisan migrate --force && php artisan serve ...` のように変更するか、デプロイ直後に Render の Shell で `php artisan migrate --force` を実行し、毎回スキーマを構築する。

## 5. 動作確認

1. Render デプロイログで `Service live` になるまで待つ。
2. HTTPS URL にアクセスして表示確認。
3. 管理画面で「Manual Deploy」を試して GitHub Actions のステータス連携が動いているか確認する。

以上で Render へ Laravel アプリをデプロイし、GitHub ベースの CI/CD を構築できます。デモ用途で無料枠を使いたい場合は、サービスがスリープする点に注意しておいてください。

## 6. よくあるハマりどころ

- **APP_KEY などの環境変数を渡し忘れる**  
  Docker ビルド時には `.env` が反映されないため、コンテナ起動時に `--env-file .env` を指定するか、ホスティング先の管理画面で `APP_KEY` を含む必要な環境変数を登録する。キーが未設定のまま `php artisan serve` を起動すると「No application encryption key has been specified.」で 500 エラーになる。
- **Vite エントリの追加漏れ**  
  テンプレートやスクリーンごとの TypeScript ファイルを `@vite(...)` で読み込む場合、`vite.config.ts` の `laravel({ input: [...] })` に忘れず追加する。開発サーバー (`npm run dev`) では動作しても、本番ビルドで `Unable to locate file in Vite manifest` が発生するので注意。
- **Sanctum のステートフルドメイン設定**  
  API 認証で `auth:sanctum` を使う場合、`SANCTUM_STATEFUL_DOMAINS` にブラウザからアクセスするホスト名・ポートを列挙する。`localhost` と `127.0.0.1` は別扱いなので、両方を `.env` で指定しておくのが実用的。環境ごとに切り替えたいので、`config/sanctum.php` へ直書きするより `.env` で制御する方が柔軟。
- **HTTPS アセットが読み込まれない**  
  `@vite` のアセット URL が `http://` になってしまう場合は、Render の Environment に `ASSET_URL=https://<service-name>.onrender.com` を追加すると正しいスキームで配信される。
