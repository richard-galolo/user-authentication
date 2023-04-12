<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});