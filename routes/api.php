<?php

use App\Http\Controllers\Api\Todo\ToggleController;
use Illuminate\Support\Facades\Route;

// 名前空間はapi.、エンドポイントは/todo/toggle
// 例: api.todo.toggle -> /todo/toggle
Route::as('api.')
    ->middleware('auth:sanctum') // sanctum 認証
    ->group(function () {
    Route::prefix('/todo')
        ->as('todo.')
        ->middleware('throttle:50,1') // レートリミット. 1分間に50回まで
        ->group(function() {
            Route::put('/toggle', ToggleController::class)
                ->name('toggle');
        });
});
