<?php

use App\Http\Middleware\EnsureEmailIsVerified;
use App\Http\Middleware\EnsurePasswordSet;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->api(prepend: [
            EnsureFrontendRequestsAreStateful::class,
        ]);

        $middleware->alias([
            'verified' => EnsureEmailIsVerified::class,
            'guest' => RedirectIfAuthenticated::class,
            'password.set' => EnsurePasswordSet::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
//        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
//            return $request->expectsJson();
//        });
    })
    ->create();
