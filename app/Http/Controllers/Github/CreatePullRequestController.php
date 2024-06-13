<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CreatePullRequestController extends Controller
{

    static string $GithubApiUrl = 'https://api.github.com';

    public function __invoke(Request $request, $repo): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required_without:issue|string',
            'head' => 'required|string',
            'head_repo' => 'string',
            'base' => 'required|string',
            'body' => 'string',
            'maintainer_can_modify' => 'boolean',
            'draft' => 'boolean',
            'issue' => 'required_without:title|integer',
        ]);

        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $response = Http::withToken($github->token)
            ->post(self::$GithubApiUrl . "/repos/{$github->provider_user_nickname}/{$repo}/pulls", $validated);

        return response()->json($response->json());
    }
}
