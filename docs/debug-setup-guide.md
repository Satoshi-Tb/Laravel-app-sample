## デバッグ環境 (Xdebug) のセットアップメモ

devcontainer 環境で VS Code のブレークポイントを効かせるまでの手順です。

1. コンテナ内で Xdebug をインストールします。未導入の場合は `pecl install xdebug` を実行します。
2. `docker-php-ext-enable xdebug`で有効化します。
3. `php --ini` で設定ファイルのパスを確認し、`/usr/local/etc/php/php.ini` が存在しなければ `cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini` などで作成します。
4. Xdebug の詳細設定は `/usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini` に追記します。最低限、以下を追加してください。
    ```ini
    zend_extension=[xdebug.soインストールパス]
    xdebug.mode=debug
    xdebug.start_with_request=yes
    xdebug.client_host=host.docker.internal
    xdebug.client_port=9003
    ```
    ※ `client_host` は利用する環境に合わせて変更してください（Docker Desktop 以外なら `127.0.0.1` など）。
5. 設定反映後、`php -m | grep Xdebug` でモジュールが読み込まれているか確認します。`phpinfo()` でも `Loaded Configuration File` と `xdebug` セクションをチェックすると確実です。
6. VS Code 側では `PHP Debug` 拡張を有効化し、`実行とデバッグ`->`launch.jsonを作成`->`PHP` をクリックし、launch.json を作成します。
7. launch.json が生成されるので「listen for xdebug」をクリックし、ひな形を選択し、設定完了。VS Code から F5 キーで debugger 起動可能になります。
8. `php artisan serve` など対象プロセスを起動し、ブレークポイントを置いてブラウザや CLI からリクエストすると停止します。

手順 3 の `docker-php-ext-xdebug.ini` を編集することでブレークポイントが効くようになる点が重要です。

## 課題
php-fpm環境でXdebug環境がうまく構築できていない。
