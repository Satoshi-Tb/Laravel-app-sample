# PHPUnit Unit Test Tips

## Database Handling

-   `use RefreshDatabase;` を付与すると、テストごとにマイグレーションを実行（もしくはトランザクションをロールバック）し、常にクリーンな SQLite インメモリ DB を用いて検証できる。
-   実物のスキーマで検証したい場合は、マイグレーション/シーダーを活用し必ずテスト前に必要なデータを投入する。
-   高速化したいテストは `DatabaseTruncation` より `RefreshDatabase` の方が負荷が少ないが、単一テストでのみ大量クエリを投げる場合は `withoutRefreshDatabase()` で局所的に外すこともできる。

## Faker と Factory

-   `Model::factory()->create()` で必要なリレーションや属性を最小限のオーバーライドで用意する。  
    例: `User::factory()->create(['email' => 'test@example.com']);`
-   Factory で定義済みの状態遷移（`->unverified()`、`->state([...])` 等）を積極的に活用し、テストごとの重複記述を減らす。

## Mocking と Service Container

-   リポジトリやサービスのモックを差し込む際は `app()->instance(Interface::class, $mock)` を使い、必要最小限のメソッドだけに期待値を設定する。
-   ただし今回の方針のように「DB へ実アクセスしたい」ケースでは、モックを使わずにスキーマ＋データ投入だけで再現できる設計を優先する。

## TestDox での説明表記

-   PHP のメソッド名は英数字と `_` のみなので、表示名を日本語にしたい場合は `#[TestDox('期待する挙動')]` 属性を付与する。  
    `php artisan test --testdox` を実行すると、テストレポートに記述した説明が表示される。
-   データプロバイダを併用する場合は、ケースの配列キー（`'正常系-締切あり' => [...]`）も TestDox 出力に表示される。

## Laravel ブートストラップ

-   `tests/CreatesApplication.php` の `createApplication()` は `bootstrap/app.php` を読み込み、コンソールカーネルをブートさせる。`Tests\TestCase` がこのトレイトを利用することで、各テスト前に Laravel アプリが完全に初期化され、ルートやサービスプロバイダを本番同様に呼び出せる。
-   `TestCase` を継承する Feature/Unit テストは、この初期化済みアプリケーションを通じて HTTP リクエストメソッド (`get`, `post`, `putJson` など) やサービスコンテナ (`app()->make()`) を利用できる。

## テスト構造

-   **Unit**: 単一クラスや純粋な関数のロジックを検証。処理時間の短さと失敗時の原因追跡しやすさを重視する。
-   **Feature**: HTTP リクエストや CLI コマンドなどアプリ全体の振る舞いを検証。Laravel ではルーティング・ミドルウェア・ビュー・DB まで含めた end-to-end の存在保証に使う。
-   データアクセスを伴うリポジトリは、複雑なクエリや再利用頻度が高い場合に Unit テストを追加し、それ以外は Feature テストでカバーするなど責務ベースで住み分ける。

## 実行とデバッグ

-   `php artisan test` は `phpunit.xml` を読み込み、SQLite インメモリ DB やキャッシュの設定を自動で適用する。CI でも同コマンドの利用がおすすめ。
-   個別テストのみ再実行したい場合は `php artisan test --filter=クラス名`、TestDox 出力確認には `--testdox` を組み合わせる。
-   Xdebug がオンの環境では `XDEBUG_MODE=off php artisan test` のように無効化すると高速化できる。失敗時に原因追跡が難しい場合は一時的にオンに戻し、`--stop-on-failure` で早期停止しながらデバッグする。

## エラー解析

-   テスト失敗時は `storage/logs/laravel.log` や `dump()`、`ray()` といったデバッグ手法を利用する。  
    PHPUnit では `->dump()` や `var_dump()` も出力されるが、ログを分離したい場合は Laravel の `Log::debug()` を活用する。
-   失敗テストが多い場合は `--stop-on-failure`、`--stop-on-error`、`--stop-on-risky` を活用し、原因を切り分けながら修正していく。
