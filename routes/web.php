<?php

use App\Http\Controllers\User\CurrentUserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return [
        'Laravel' => app()->version(),
        'PHP' => PHP_VERSION,
        'Framework' => 'Laravel',
        'Status' => 'OK',
        'State' => [
            'Environment' => config('app.env'),
            'Debug' => config('app.debug') ? 'Enabled' : 'Disabled',
            'URL' => config('app.url'),
            'Timezone' => config('app.timezone'),
            'Locale' => config('app.locale'),
            'Database' => config('database.default'),
            'Cache' => config('cache.default'),
            'Queue' => config('queue.default'),
            'Session' => config('session.driver'),
        ],
        'User' => [
            'Authenticated' => auth()->check() ? 'Yes' : 'No',
            'User' => auth()->check() ? auth()->user() : null,
        ]
    ];
});

require __DIR__ . '/auth.php';

Route::get('/user', [CurrentUserController::class, 'index'])->middleware('auth:sanctum');
