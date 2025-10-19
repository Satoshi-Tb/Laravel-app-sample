# Render へのデプロイ & GitHub 連携 CI/CD 手順（Free プラン想定）

## 前提

- Render アカウントと GitHub アカウントを用意し、当リポジトリを GitHub 上に配置しておく。
- リポジトリ直下の `Dockerfile` や `docker/render/*` は Render 用に調整済みである前提。
- Free Web Service を利用するため、SQLite データベースはデプロイやスリープ復帰のたびに初期化されても許容できること。

## 1. Docker イメージ構成

本リポジトリの `Dockerfile` はマルチステージ構成で Vite ビルド済みアセットを含む PHP-FPM + Nginx 画像を作成します。Render の `PORT`（8080）が Listen されるよう Nginx を同梱し、エントリポイントでマイグレーションまで実行します。

```dockerfile
# syntax=docker/dockerfile:1

FROM node:20-alpine AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.ts tsconfig.json ./
COPY resources ./resources
COPY public ./public
RUN npm run build

FROM php:8.3-fpm-alpine AS production
WORKDIR /var/www/html
RUN apk add --no-cache git curl unzip sqlite sqlite-dev nginx
RUN docker-php-ext-install pdo_sqlite
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --ansi --no-scripts
COPY . ./
COPY --from=frontend /app/public/build ./public/build
RUN rm -f bootstrap/cache/*.php && php artisan package:discover --ansi
RUN chown -R www-data:www-data storage bootstrap/cache database
RUN rm -f /etc/nginx/http.d/default.conf \
    && mkdir -p /run/nginx \
    && ln -sf /dev/stdout /var/log/nginx/access.log \
    && ln -sf /dev/stderr /var/log/nginx/error.log
COPY docker/render/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/render/php-fpm/www.conf /usr/local/etc/php-fpm.d/zz-www.conf
COPY docker/render/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
ENV PORT=8080
EXPOSE 8080
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
```

- `docker/render/entrypoint.sh` は Render 起動時に以下を実行します。
  - `php artisan config:cache`・`route:cache`（`APP_KEY` が設定されている場合）
  - `touch database/database.sqlite` → `php artisan migrate --force`
  - 権限調整後に PHP-FPM と Nginx を起動
- `docker/render/nginx/default.conf` は `listen 8080;` で Render 固定ポートを待ち受けます。

### ローカルでの動作確認

Render と同じエントリポイントを使ってローカル確認できます。

```bash
docker build -t laravel-app:render .
docker run --rm \
  -p 8080:8080 \
  -e APP_KEY="$(php artisan key:generate --show)" \
  -e APP_ENV=local \
  -e APP_DEBUG=true \
  laravel-app:render
```

初回起動時にマイグレーションが走り、`http://localhost:8080` でアプリを確認できます。終了時は `Ctrl+C`。追加で生成されたイメージやコンテナは不要であれば削除してください。

## 2. Render Web Service の作成

1. Render ダッシュボードで **New → Web Service** を選択し、対象リポジトリを接続。
2. Branch はデプロイ対象（例: `main`）を選ぶ。
3. Environment は **Docker** を選択。Build Command / Start Command は空欄で OK（前述の Dockerfile と entrypoint をそのまま利用）。Render 側では `PORT` が 8080 に固定されているので変更不要。
4. Free Web Service では Persistent Disk が使えない点に注意。
5. 環境変数に `.env` 相当を設定する。最低限:
   - `APP_KEY`（ローカルで `php artisan key:generate --show` した値）
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `APP_URL=https://<service-name>.onrender.com`
   - `ASSET_URL=https://<service-name>.onrender.com`
   - `SANCTUM_STATEFUL_DOMAINS=<service-name>.onrender.com`
   - ローカル動作用に `SANCTUM_STATEFUL_DOMAINS` を共用する場合は `localhost:8080,127.0.0.1:8080` も含めてカンマ区切りで列挙する
6. **Create Web Service** をクリックすると初回デプロイが実行され、完了後に HTTPS URL が払い出される。

## 3. SQLite を用いた Free プラン運用

- Free プランではファイルシステムがデプロイごと・スリープ復帰ごとに初期化される。エントリポイントで毎回 `database/database.sqlite` を作成しているため、SQLite の内容は永続化されない想定。
- マイグレーションはエントリポイント内で `php artisan migrate --force` を実行しているため、特別な Start Command 追加は不要。初期データが必要なら `DatabaseSeeder` にロジックを追加しておくと起動ごとに投入される。
- 永続化が必要になった場合は Render の PostgreSQL / MySQL 外部 DB を契約し、接続文字列を `DB_CONNECTION=pgsql` などに切り替える。

## 4. GitHub Actions 連携による自動デプロイ

Render は対象ブランチへ push されるたびに自動デプロイを行いますが、GitHub Actions でテストを通過した場合のみデプロイする設定も可能です。例として以下のワークフローを用意しておくと、テスト成功後に Render API でデプロイをトリガーできます。

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

- `RENDER_SERVICE_ID` と `RENDER_API_KEY` は Render ダッシュボード > Service Settings > **Deploy Hooks** から取得し、GitHub リポジトリの Secrets に登録する。
- Render 側の **Manual Deploys** 設定で「Allow builds only after successful checks」を有効化すると、GitHub Checks の成功を待ってからデプロイされる。

## 5. デプロイ後の確認手順

1. Render のデプロイログで `Service live` になるまで待つ。
2. 公開 URL `https://<service-name>.onrender.com` にアクセスし、アプリが表示されるか確認。
3. 認証付き API（例: `/api/todo/toggle`）を叩き、Sanctum Cookie が正しく付与されているか確認。必要に応じてブラウザのアプリケーションストレージを確認。
4. 「Manual Deploy」を試して GitHub Actions のステータス連携が期待どおりに働くか検証。

## 6. よくあるハマりどころ

- **環境変数の設定漏れ**  
  `APP_KEY` や `APP_URL`、`SANCTUM_STATEFUL_DOMAINS` を Render 上で設定し忘れると、500 エラーや 401 エラーが発生する。特に Sanctum は `localhost` と `127.0.0.1` を別ホストとして扱うため、それぞれのポートを列挙する。

- **アセット URL のスキーム違い**  
  本番 URL が HTTPS のため、`ASSET_URL` を Render ドメインで指定しないと Vite ビルド済みアセットが HTTP で参照され混在コンテンツ扱いになる。

- **Render 固定ポートを使っていない**  
  Free プランの Web Service は `PORT` 環境変数で指定されたポートで Listen する必要がある。`docker/render/nginx/default.conf` は `listen 8080;` になっているため、独自に `php artisan serve` を起動するなどポートを変えるとヘルスチェックに失敗する。

- **外部永続化をしていないのにデータが消える**  
  Free プランではスリープ復帰や再デプロイで SQLite がリセットされる仕様。永続化が必要なら Persistent Disk（Starter プラン以上）か外部 DB を選択する。

- **Vite エントリの追加漏れ**  
  新しいエントリポイントを追加した際は `vite.config.ts` の `laravel({ input: [...] })` に忘れず追記する。ローカル開発では動作しても本番ビルドでは `Unable to locate file in Vite manifest` が起きる。

- **WSL2を利用する場合の、nginxの権限設定について**  
  nginxが参照するフォルダには、www-dataの権限が必要になる。bind mountを利用する場合、すでにWindows側で作成済のファイルの場合、Dockerファイルにオーナー変更処理を記載していても反映されない場合がある。その場合、コンテナ接続後に所有権変更コマンドを発行すれば解決できる。
