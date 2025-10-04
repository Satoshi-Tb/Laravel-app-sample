<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SignupController;
use App\Http\Controllers\Todo\CreateController;
use App\Http\Controllers\Todo\DeleteController;
use App\Http\Controllers\Todo\EditController;
use App\Http\Controllers\Todo\IndexController;
use App\Http\Controllers\Todo\NewController;
use App\Http\Controllers\Todo\UpdateController;

 Route::get('/', HomeController::class)->name('home');

 Route::get('/login', LoginController::class)->name('login');

 Route::get('/signup', SignupController::class)->name('signup');

 Route::prefix('/todo')
     ->as('todo.')
     ->group(function () {
        Route::get('/', IndexController::class)->name('index');
        Route::get('/new', NewController::class)->name('new');
        Route::get('/edit/{id}', EditController::class)->name('edit');
        Route::post('/create', CreateController::class)->name('create');
        Route::put('/update', UpdateController::class)->name('update');
        Route::delete('/delete', DeleteController::class)->name('delete');
     });
