<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;

class GithubRepoController extends Controller
{
    /**
     * @throws ConnectionException
     */
    public function index(Request $request)
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $token = $github->token;

        $response = Http::withToken($token)->get('https://api.github.com/user/repos');

        return $response->json();
    }

    public function redirect()
    {
        return Socialite::driver('github')
            ->scopes(['repo'])
            ->redirect();
    }
}
