<?php

use Illuminate\Support\Facades\Route;

// 名前空間はapi.、エンドポイントは/todo/toggle
// 例: api.todo.toggle -> /todo/toggle
Route::as('api.')->group(function () {
    Route::prefix('/todo')
        ->as('todo.')
        ->group(function() {
            Route::get('/toggle', function () {
                return ['test' => 'API TEST'];
            })->name('toggle');
        });
});
