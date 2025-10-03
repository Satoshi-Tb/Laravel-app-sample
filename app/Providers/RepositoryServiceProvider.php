<?php

namespace App\Providers;

use App\Repositories\Repository;
use App\Repositories\TodoRepository;
use Illuminate\Support\ServiceProvider;
use App\Http\Controllers\Todo;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->when([
                Todo\IndexController::class,
                Todo\NewController::class,
                Todo\EditController::class,
                Todo\CreateController::class,
                Todo\UpdateController::class,
                Todo\DeleteController::class,
            ])
            ->needs(Repository::class)
            ->give(fn() => new TodoRepository());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
