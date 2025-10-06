# Laravel Sanctum ステートフル認証ガイド

## 1. ステートフル認証の概要
- Laravel Sanctum で SPA やモバイル向けに採用する「ステートフル」モードは、セッション + HTTP-only クッキーを用いてユーザーを認証する方式です。
- API 呼び出しをセッション認証に繋げることで、Laravel の `web` ガードと CSRF 保護をそのまま利用できます。

## 2. `statefulApi()` の役割
- `bootstrap/app.php` の `statefulApi()` を呼ぶことで、Sanctum が `EnsureFrontendRequestsAreStateful` ミドルウェアを API スタックへ追加します。
- このミドルウェアは、リクエスト元のドメインが `config/sanctum.php` の `stateful` に含まれているかを確認し、該当する場合はセッション (`web` ガード) で認証処理を行います。
- また、SPA からのミューテーションリクエスト（POST/PUT/DELETE など）に対して CSRF トークン (`X-XSRF-TOKEN` ヘッダー) を検証し、攻撃を防ぎます。

## 3. `axios.defaults.withCredentials = true` の意味
- ブラウザやツールからのクロスオリジン通信では、デフォルトでクッキーが送受信されません。
- `axios.defaults.withCredentials = true;` を設定することで、`laravel_session` などの HTTP-only クッキーを API リクエストに添付し、レスポンスで配布されるクッキー (`XSRF-TOKEN`) も保存されます。
- この設定により、SPA からの API 呼び出し時にサーバー側でセッションを復元できるようになります。

## 4. `/sanctum/csrf-cookie` エンドポイント
- Sanctum が提供する初期化用エンドポイントで、GET リクエストすると以下を行います。
  - `XSRF-TOKEN` クッキーを発行（JavaScript から読み取り可能な CSRF トークン用クッキー）。
  - `laravel_session` クッキーを設定し、セッションを開始。
- フロントエンドは API を叩く前に一度このエンドポイントを呼び出すことで、CSRF トークンとセッションを準備できます。
- axios は `withCredentials` 設定を通じてこのクッキーを保存し、以降のリクエストで `X-XSRF-TOKEN` ヘッダーを自動付与します。

## 5. 未ログイン時の挙動
- ログインしていない状態で保護済み API (`/api/...`) を呼び出すと、Sanctum がセッション認証に失敗し、401 もしくは 403 を返します。
- ログインフォーム等を経由してセッションを確立した後に再度 API を呼び出せば、クッキー経由で認証済みとして扱われます。

## 6. Postman でのテスト手順
1. **環境設定**
   - `config/sanctum.php` の `stateful` 配列に Postman からアクセスするホスト名（例: `localhost`, `127.0.0.1`）を含める。
   - `.env` の `SESSION_DOMAIN`、`SANCTUM_STATEFUL_DOMAINS` などがローカルテスト向けに調整されているか確認。
2. **CSRF/セッションの初期化**
   - Postman で `GET /sanctum/csrf-cookie` を実行し、Cookie Jar に `XSRF-TOKEN`, `laravel_session` が保存されたことを確認。
3. **ログインリクエスト**
   - `POST /login` を送信。`X-XSRF-TOKEN` ヘッダーに Cookie Jar の `XSRF-TOKEN` 値（URLデコードしたもの）を設定する。
   - レスポンスで `laravel_session` が更新されていればログイン成功。
4. **保護された API の呼び出し**
   - 同一コレクション内で API (`/api/...`) を呼び出す。Postman は Cookie を自動添付するため、`X-XSRF-TOKEN` ヘッダーを設定しておけば 200 系レスポンスが得られる。
   - 未ログインの状態を再現したい場合は Cookie を削除するか、`POST /logout` を呼び出してセッションを破棄する。

## 7. 補足
- アクセストークンを利用した Bearer 認証は、手動発行したパーソナルアクセストークンを使う事で引き続き可能ですが、SPA の通常運用ではセッション + クッキー運用が推奨されます。
- テスト後は Cookie のクリアやログアウトを行い、再テスト時に状態が引きずられないよう管理してください。
