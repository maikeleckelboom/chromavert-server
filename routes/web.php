<?php

use App\Http\Controllers\User\CurrentUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

require __DIR__ . '/auth.php';

Route::get('/user', [CurrentUserController::class, 'index'])->middleware('auth:sanctum');
