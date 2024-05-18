<?php

use App\Http\Controllers\Account\AuthProviderController;
use App\Http\Controllers\Account\DatabaseSessionController;
use App\Http\Controllers\Account\ProfileInformationController;
use App\Http\Controllers\Account\UpdatePasswordController;
use App\Http\Controllers\Auth\CurrentUserController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/user', [CurrentUserController::class, 'index']);
    Route::delete('/user', [ProfileInformationController::class, 'destroy']);
    Route::put('/user/profile-information', [ProfileInformationController::class, 'update']);

    Route::put('/user/password', [UpdatePasswordController::class, 'update']);

    Route::get('/user/providers', [AuthProviderController::class, 'index']);
    Route::delete('/user/providers/{id}', [AuthProviderController::class, 'disconnect']);

    Route::get('/user/sessions', [DatabaseSessionController::class, 'index']);
    Route::delete('/user/sessions/{id}', [DatabaseSessionController::class, 'destroy']);
    Route::delete('/user/other-sessions', [DatabaseSessionController::class, 'destroyOtherSessions']);

    Route::group(['middleware' => 'root'], function () {
        Route::get('/root/users', [UserController::class, 'index']);
    });
});



Route::get('/', function () {
    return [
        'PHP' => PHP_VERSION,
        'Laravel' => app()->version(),
    ];
});

