<?php

use App\Http\Controllers\Auth\UpdatePasswordController;
use App\Http\Controllers\User\GithubRepoController;
use App\Http\Controllers\User\IdentityProviderController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\SessionController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/user', [UserController::class, 'index']);
    Route::delete('/user', [ProfileController::class, 'destroy']);
    Route::put('/user/profile', [ProfileController::class, 'update']);
    Route::put('/user/password', [UpdatePasswordController::class, 'update']);

    Route::get('/user/identity-providers', [IdentityProviderController::class, 'index']);
    Route::delete('/user/identity-providers/{id}', [IdentityProviderController::class, 'disconnect']);

    Route::get('/user/sessions', [SessionController::class, 'index']);
    Route::delete('/user/sessions/{id}', [SessionController::class, 'destroy']);
    Route::delete('/user/other-sessions', [SessionController::class, 'destroyOtherSessions']);

    Route::get('/user/github-repos', [GithubRepoController::class, 'index']);

    Route::get('/user/github-repos/callback', [GithubRepoController::class, 'redirect']);
});


/**
 * Do NOT forget to remove this route in production
 *
 * ** This route is for development purposes only **
 * _________________________________________________
 */
Route::get('/symlink', fn() => Artisan::call('storage:link'));
Route::get('/', fn() => ['PHP' => PHP_VERSION, 'Laravel' => app()->version()]);

