<?php

use App\Http\Controllers\Auth\AuthProviderController;
use App\Http\Controllers\Auth\DatabaseSessionController;
use App\Http\Controllers\Auth\DestroyOtherSessionsController;
use App\Http\Controllers\Auth\UpdatePasswordController;
use App\Http\Controllers\CurrentUserController;
use App\Http\Controllers\ProfileInformationController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/user', [CurrentUserController::class, 'index']);
    Route::delete('/user', [ProfileInformationController::class, 'destroy']);
    Route::put('/user/password', [UpdatePasswordController::class, 'update']);
    Route::put('/user/profile-information', [ProfileInformationController::class, 'update']);
    Route::get('/user/providers', [AuthProviderController::class, 'index']);
    Route::delete('/user/providers/{id}', [AuthProviderController::class, 'disconnect']);
    Route::get('/user/sessions', [DatabaseSessionController::class, 'index']);
    Route::delete('/user/sessions/{id}', [DatabaseSessionController::class, 'destroy']);
    Route::delete('/user/other-sessions', DestroyOtherSessionsController::class);

    Route::get('/maintenance', fn() => response()->json(app()->isDownForMaintenance()))->name('maintenance');
});



Route::get('/', function () {
    return [
        'PHP' => PHP_VERSION,
        'Laravel' => app()->version(),
    ];
});

