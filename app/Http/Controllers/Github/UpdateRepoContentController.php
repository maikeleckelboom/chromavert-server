<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UpdateRepoContentController extends Controller
{
    static string $GithubReposUrl = 'https://api.github.com/repos';

    /**
     * @throws ConnectionException
     */
    public function __invoke(Request $request, $repo, $path): JsonResponse
    {
        $github = $request->user()->identityProviders()->where('provider', 'github')->first();
        $username = $github->provider_user_nickname;

        $fetcher = Http::withToken($github->token);
        $endpoint = self::$GithubReposUrl . "/{$username}/{$repo}/contents/{$path}";

        $response = $fetcher->put($endpoint, [
            'sha' => $request->get('sha'),
            'message' => $request->get('message'),
            'branch' => $request->get('branch', 'main'),
            'content' => base64_encode($request->get('content')),
        ]);

        return response()->json($response->json());
    }
}
