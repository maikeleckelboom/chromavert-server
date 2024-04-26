<?php

use App\Http\Controllers\Auth\AuthProviderController;
use App\Http\Controllers\User\CurrentUserController;
use App\Http\Controllers\User\DatabaseSessionsController;
use App\Http\Controllers\User\RevokeOtherSessionsController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', [CurrentUserController::class, 'index']);

    Route::get('/user/providers', [AuthProviderController::class, 'index']);
    Route::delete('/user/providers/{id}', [AuthProviderController::class, 'disconnect']);

    Route::get('/user/sessions', [DatabaseSessionsController::class, 'index']);
    Route::delete('/user/sessions/{id}', [DatabaseSessionsController::class, 'destroy']);
    Route::delete('/user/other-browser-sessions', RevokeOtherSessionsController::class);

    Route::delete('/user', [CurrentUserController::class, 'destroy']);
    Route::put('/user', [CurrentUserController::class, 'update']);

    Route::get('/user/profile', [CurrentUserController::class, 'getProfileInformation']);


});
