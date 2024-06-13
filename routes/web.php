<?php

use App\Http\Controllers\Auth\UpdatePasswordController;
use App\Http\Controllers\Github\CreateBranchController;
use App\Http\Controllers\Github\CreatePullRequestController;
use App\Http\Controllers\Github\RepoContentController;
use App\Http\Controllers\Github\RepoController;
use App\Http\Controllers\Github\SearchGithubRepoController;
use App\Http\Controllers\Github\UpdateRepoContentController;
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


    Route::get('/user/repos', [RepoController::class, 'index']);
    Route::get('/user/repos/{repo}', [RepoController::class, 'show']);

    Route::post('/user/repos/{repo}/git/refs', CreateBranchController::class);

    Route::post('/user/repos/{repo}/pulls', CreatePullRequestController::class);


    Route::get('/user/repos/{repo}/css', SearchGithubRepoController::class);
    Route::get('/user/repos/{repo}/contents/{path?}', [RepoContentController::class, 'paths'])->where('path', '.*');
    Route::put('/user/repos/{repo}/contents/{path?}', UpdateRepoContentController::class)->where('path', '.*');


    Route::get('/user/repos/{repo}/branches', [RepoContentController::class, 'branches']);
    Route::get('/user/repos/{repo}/commits', [RepoContentController::class, 'commits']);
});


/**
 * Do NOT forget to remove this route in production
 *
 * ** This route is for development purposes only **
 * _________________________________________________
 */
Route::get('/symlink', fn() => Artisan::call('storage:link'));
Route::get('/', fn() => ['PHP' => PHP_VERSION, 'Laravel' => app()->version()]);

