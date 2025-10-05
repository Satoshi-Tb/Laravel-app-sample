<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\Todo;
use App\Http\Controllers\Auth;
use App\Http\Controllers\User;

 Route::get('/', HomeController::class)->name('home');

 Route::get('/login', LoginController::class)->name('login');

 Route::get('/signup', SignupController::class)->name('signup');


Route::prefix('/auth')
    ->as('auth.')
    ->group(function () {
        Route::post('/login', Auth\LoginController::class)->name('login');
    });

Route::prefix('/user')
    ->as('user.')
    ->group(function() {
        Route::post('/create', User\CreateController::class)->name('create');
    });

 Route::prefix('/todo')
     ->as('todo.')
     ->middleware('auth') // ログインしていない場合、login ページへリダイレクト
     ->group(function () {
        Route::get('/', TODO\IndexController::class)->name('index');
        Route::get('/new', TODO\NewController::class)->name('new');
        Route::get('/edit/{id}', TODO\EditController::class)->name('edit');
        Route::post('/create', TODO\CreateController::class)->name('create');
        Route::put('/update', TODO\UpdateController::class)->name('update');
        Route::delete('/delete', TODO\DeleteController::class)->name('delete');
     });
