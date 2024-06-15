<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchGithubController extends Controller
{
    static string $GithubSearchUrl = 'https://api.github.com/search';

    /**
     * @throws ConnectionException
     */
    public function searchUserRepositories(Request $request): JsonResponse
    {
        $github = $request->user()->identityProviders()->where('provider', 'github')->first();

        $query = $request->get('q');
        $username = $github->provider_user_nickname;

        $fetcher = Http::withToken($github->token);
        $response = $fetcher->get(self::$GithubSearchUrl . "/repositories?q={$query}+user:{$username}&sort=updated");

        return response()->json($response->json());
    }

    /**
     * @throws ConnectionException
     */
    public function searchCodeInUserRepository(Request $request): JsonResponse
    {
        $github = $request->user()->identityProviders()->where('provider', 'github')->first();

        $query = $request->get('q');
        $repo = $request->get('repo');
        $username = $github->provider_user_nickname;

        $fetcher = Http::withToken($github->token);
        $response = $fetcher->get(self::$GithubSearchUrl . "/code?q={$query}+repo:{$username}/{$repo}");

        return response()->json($response->json());
    }
}
