<?php

use App\Http\Controllers\Account\AuthProviderController;
use App\Http\Controllers\Account\CurrentUserController;
use App\Http\Controllers\Account\BrowserSessionController;
use App\Http\Controllers\Account\ProfileInformationController;
use App\Http\Controllers\Account\UpdatePasswordController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::get('/user', [CurrentUserController::class, 'index']);
    Route::delete('/user', [ProfileInformationController::class, 'destroy']);
    Route::put('/user/profile-information', [ProfileInformationController::class, 'update']);
    Route::put('/user/password', [UpdatePasswordController::class, 'update']);

    //* Maybe identity_provider */
    Route::get('/user/providers', [AuthProviderController::class, 'index']);
    Route::delete('/user/providers/{id}', [AuthProviderController::class, 'disconnect']);

    Route::get('/user/sessions', [BrowserSessionController::class, 'index']);
    Route::delete('/user/sessions/{id}', [BrowserSessionController::class, 'destroy']);
    Route::delete('/user/other-sessions', [BrowserSessionController::class, 'destroyOtherSessions']);


    Route::get('/admin/users', [UserController::class, 'index'])->middleware('admin');

});

// DEVELOPMENT ONLY
Route::get('/symlink', fn() => Artisan::call('storage:link'));
Route::get('/', fn() => [
    'PHP' => PHP_VERSION,
    'Laravel' => app()->version(),
]);

