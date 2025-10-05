<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

-   [Simple, fast routing engine](https://laravel.com/docs/routing).
-   [Powerful dependency injection container](https://laravel.com/docs/container).
-   Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
-   Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
-   Database agnostic [schema migrations](https://laravel.com/docs/migrations).
-   [Robust background job processing](https://laravel.com/docs/queues).
-   [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

-   **[Vehikl](https://vehikl.com)**
-   **[Tighten Co.](https://tighten.co)**
-   **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
-   **[64 Robots](https://64robots.com)**
-   **[Curotec](https://www.curotec.com/services/technologies/laravel)**
-   **[DevSquad](https://devsquad.com/hire-laravel-developers)**
-   **[Redberry](https://redberry.international/laravel-development)**
-   **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

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
