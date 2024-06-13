<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CreateBranchController extends Controller
{
    static string $GithubReposUrl = 'https://api.github.com/repos';

    /**
     * @throws ConnectionException
     */
    public function __invoke(Request $request, $repo): JsonResponse
    {
        $validated = $request->validate([
            'ref' => 'required|string'
        ]);

        $github = $request->user()->identityProviders()->where('provider', 'github')->first();

        $baseUrl = self::$GithubReposUrl . "/{$github->provider_user_nickname}/{$repo}";

        $fetcher = Http::withToken($github->token);

        $sha = $fetcher->get("{$baseUrl}/git/refs/heads/main")->json();

        $response = $fetcher->post($baseUrl . "/git/refs", [
            'owner' => $github->provider_user_nickname,
            'repo' => $repo,
            'ref' => "refs/heads/{$validated['ref']}",
            'sha' => $sha['object']['sha']
        ]);

        return response()->json($response->json());
    }

}
