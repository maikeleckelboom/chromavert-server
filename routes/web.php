<?php

use App\Http\Controllers\Auth\AuthProviderController;
use App\Http\Controllers\User\CurrentUserController;
use App\Http\Controllers\User\DatabaseSessionController;
use App\Http\Controllers\User\DestroyOtherSessionsController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';


Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', [CurrentUserController::class, 'index']);
    Route::delete('/user', [CurrentUserController::class, 'destroy']);
    Route::put('/user', [CurrentUserController::class, 'update']);
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
        'Laravel' => app()->version(),
        'PHP' => PHP_VERSION,
        'Framework' => 'Laravel',
        'Status' => 'OK',
        'Database' => config('database.default'),
        'Cache' => config('cache.default'),
        'Queue' => config('queue.default'),
        'Session' => config('session.driver'),
        'Env' => [
            'APP_KEY' => config('app.APP_KEY'),
            'APP_LOCALE' => config('app.APP_LOCALE'),
            'APP_TIMEZONE' => config('app.APP_TIMEZONE'),
            'APP_NAME' => config('app.APP_NAME'),
            'APP_URL' => config('app.APP_URL'),
            'FRONTEND_URL' => config('app.FRONTEND_URL'),
            'DB_CONNECTION' => config('app.DB_CONNECTION'),
            'DB_HOST' => config('app.DB_HOST'),
            'DB_PORT' => config('app.DB_PORT'),
            'DB_DATABASE' => config('app.DB_DATABASE'),
            'DB_USERNAME' => config('app.DB_USERNAME'),
            'DB_PASSWORD' => config('app.DB_PASSWORD'),
            'DATABASE_URL' => config('app.DATABASE_URL'),
            'REDIRECT_ON_LOGOUT' => config('app.REDIRECT_ON_LOGOUT'),
            'REDIRECT_ON_AUTH_ONLY' => config('app.REDIRECT_ON_AUTH_ONLY'),
            'REDIRECT_ON_LOGIN' => config('app.REDIRECT_ON_LOGIN'),
            'REDIRECT_ON_GUEST_ONLY' => config('app.REDIRECT_ON_GUEST_ONLY'),
            'SANCTUM_STATEFUL_DOMAINS' => config('app.SANCTUM_STATEFUL_DOMAINS'),
            'SESSION_DOMAIN' => config('app.SESSION_DOMAIN'),
            'SESSION_DRIVER' => config('app.SESSION_DRIVER'),
            'SESSION_LIFETIME' => config('app.SESSION_LIFETIME'),
            'SESSION_ENCRYPT' => config('app.SESSION_ENCRYPT'),
            'SESSION_PATH' => config('app.SESSION_PATH'),
            'MAIL_MAILER' => config('app.MAIL_MAILER'),
            'MAIL_HOST' => config('app.MAIL_HOST'),
            'MAIL_PORT' => config('app.MAIL_PORT'),
            'MAIL_USERNAME' => config('app.MAIL_USERNAME'),
            'MAIL_PASSWORD' => config('app.MAIL_PASSWORD'),
            'MAIL_ENCRYPTION' => config('app.MAIL_ENCRYPTION'),
            'MAIL_FROM_ADDRESS' => config('app.MAIL_FROM_ADDRESS'),
            'MAIL_FROM_NAME' => config('app.MAIL_FROM_NAME'),
            'CACHE_DRIVER' => config('app.CACHE_DRIVER'),
            'NUXT_PUBLIC_SANCTUM_BASE_URL' => config('app.NUXT_PUBLIC_SANCTUM_BASE_URL'),
            'GITHUB_CLIENT_ID' => config('app.GITHUB_CLIENT_ID'),
            'GITHUB_CLIENT_SECRET' => config('app.GITHUB_CLIENT_SECRET'),
            'GOOGLE_REDIRECT_URI' => config('app.GOOGLE_REDIRECT_URI'),
            'GOOGLE_CLIENT_ID' => config('app.GOOGLE_CLIENT_ID'),
            'GOOGLE_CLIENT_SECRET' => config('app.GOOGLE_CLIENT_SECRET'),
        ],
    ];
});

