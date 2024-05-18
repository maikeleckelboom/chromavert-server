<?php

use App\Http\Controllers\CurrentUserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', [CurrentUserController::class, 'index']);
});
