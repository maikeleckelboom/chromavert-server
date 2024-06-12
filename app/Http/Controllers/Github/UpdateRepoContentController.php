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
        $github = $request->user()->identityProviders()
            ->where('provider', 'github')->first();

        $fetchService = Http::withToken($github->token);

        $baseURL = self::$GithubReposUrl . "/{$github->provider_user_nickname}/{$repo}/contents/{$path}";

        $response = $fetchService->put($baseURL, [
            'sha' => $request->get('sha'),
            'message' => $request->get('message'),
            'branch' => $request->get('branch', 'main'),
            'content' => base64_encode($request->get('content')),
        ]);

        return response()->json($response->json());
    }
}
