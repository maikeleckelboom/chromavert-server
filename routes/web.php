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
            'APP_KEY' => env('app.APP_KEY'),
            'APP_LOCALE' => env('app.APP_LOCALE'),
            'APP_TIMEZONE' => env('app.APP_TIMEZONE'),
            'APP_NAME' => env('app.APP_NAME'),
            'APP_URL' => env('app.APP_URL'),
            'FRONTEND_URL' => env('app.FRONTEND_URL'),
            'DB_CONNECTION' => env('app.DB_CONNECTION'),
            'DB_HOST' => env('app.DB_HOST'),
            'DB_PORT' => env('app.DB_PORT'),
            'DB_DATABASE' => env('app.DB_DATABASE'),
            'DB_USERNAME' => env('app.DB_USERNAME'),
            'DB_PASSWORD' => env('app.DB_PASSWORD'),
            'DATABASE_URL' => env('app.DATABASE_URL'),
            'REDIRECT_ON_LOGOUT' => env('app.REDIRECT_ON_LOGOUT'),
            'REDIRECT_ON_AUTH_ONLY' => env('app.REDIRECT_ON_AUTH_ONLY'),
            'REDIRECT_ON_LOGIN' => env('app.REDIRECT_ON_LOGIN'),
            'REDIRECT_ON_GUEST_ONLY' => env('app.REDIRECT_ON_GUEST_ONLY'),
            'SANCTUM_STATEFUL_DOMAINS' => env('app.SANCTUM_STATEFUL_DOMAINS'),
            'SESSION_DOMAIN' => env('app.SESSION_DOMAIN'),
            'SESSION_DRIVER' => env('app.SESSION_DRIVER'),
            'SESSION_LIFETIME' => env('app.SESSION_LIFETIME'),
            'SESSION_ENCRYPT' => env('app.SESSION_ENCRYPT'),
            'SESSION_PATH' => env('app.SESSION_PATH'),
            'MAIL_MAILER' => env('app.MAIL_MAILER'),
            'MAIL_HOST' => env('app.MAIL_HOST'),
            'MAIL_PORT' => env('app.MAIL_PORT'),
            'MAIL_USERNAME' => env('app.MAIL_USERNAME'),
            'MAIL_PASSWORD' => env('app.MAIL_PASSWORD'),
            'MAIL_ENCRYPTION' => env('app.MAIL_ENCRYPTION'),
            'MAIL_FROM_ADDRESS' => env('app.MAIL_FROM_ADDRESS'),
            'MAIL_FROM_NAME' => env('app.MAIL_FROM_NAME'),
            'CACHE_DRIVER' => env('app.CACHE_DRIVER'),
            'NUXT_PUBLIC_SANCTUM_BASE_URL' => env('app.NUXT_PUBLIC_SANCTUM_BASE_URL'),
            'GITHUB_CLIENT_ID' => env('app.GITHUB_CLIENT_ID'),
            'GITHUB_CLIENT_SECRET' => env('app.GITHUB_CLIENT_SECRET'),
            'GOOGLE_REDIRECT_URI' => env('app.GOOGLE_REDIRECT_URI'),
            'GOOGLE_CLIENT_ID' => env('app.GOOGLE_CLIENT_ID'),
            'GOOGLE_CLIENT_SECRET' => env('app.GOOGLE_CLIENT_SECRET'),
        ],
    ];
});

