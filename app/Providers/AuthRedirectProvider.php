<?php

namespace App\Providers;

class AuthRedirectProvider
{
    public static function getRoute(string $key): string
    {
        $key = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
        return config('app.redirects.' . $key);
    }
}
