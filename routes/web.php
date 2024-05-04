<?php

use App\Http\Controllers\Auth\AuthProviderController;
use App\Http\Controllers\Auth\DatabaseSessionController;
use App\Http\Controllers\Auth\DestroyOtherSessionsController;
use App\Http\Controllers\CurrentUserController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::resource('/user', CurrentUserController::class)->only(['index', 'destroy', 'update']);
    Route::group(['prefix' => 'user'], function () {
        Route::get('/providers', [AuthProviderController::class, 'index']);
        Route::delete('/providers/{id}', [AuthProviderController::class, 'disconnect']);
        Route::get('/sessions', [DatabaseSessionController::class, 'index']);
        Route::delete('/sessions/{id}', [DatabaseSessionController::class, 'destroy']);
        Route::delete('/other-sessions', DestroyOtherSessionsController::class);
    });
});

Route::get('/', function () {
    return [
        'PHP' => PHP_VERSION,
        'Laravel' => app()->version(),
    ];
});

