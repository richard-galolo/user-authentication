<?php

use App\Http\Controllers\Api\V1\Auth\AuthenticatedApiController;
use App\Http\Controllers\Api\V1\Auth\NewPasswordApiController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetApiController;
use App\Http\Controllers\Api\V1\Auth\RefreshTokenApiController;
use App\Http\Controllers\Api\V1\Auth\RegisteredUserApiController;
use App\Http\Controllers\Api\V1\ProfileApiController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'v1', 'as' => 'api.'], function () {
    Route::post('register', [RegisteredUserApiController::class, 'store'])->name('register');

    Route::post('login', [AuthenticatedApiController::class, 'store'])->name('login');

    Route::post('forgot-password', [PasswordResetApiController::class, 'store'])->name('forgot_password');

    Route::post('reset-password', [NewPasswordApiController::class, 'store'])->name('reset_password');

    Route::post('refresh-token', [RefreshTokenApiController::class, 'store'])
        ->middleware('auth:sanctum')
        ->name('refresh_token');

    Route::delete('logout', [AuthenticatedApiController::class, 'destroy'])
        ->middleware('auth:sanctum')
        ->name('logout');

    Route::get('profile', [ProfileApiController::class, 'show'])
        ->middleware('auth:sanctum')
        ->name('profile.show');
    Route::patch('profile', [ProfileApiController::class, 'update'])
        ->middleware('auth:sanctum')
        ->name('profile.update');
});