<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchGithubRepoController extends Controller
{
    public function __invoke(Request $request, $repo): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $query = collect(['css', 'scss', 'sass', 'less', 'pcss'])
            ->map(fn($extension) => "repo:{$github->provider_user_nickname}/{$repo}+extension:{$extension}")
            ->implode('+OR+');

        $response = Http::withToken($github->token)
            ->get("https://api.github.com/search/code?q={$query}");

        $total_count = $response->json()['total_count'];
        $incomplete_results = $response->json()['incomplete_results'];

        $cssFiles = collect($response->json()['items'])
            ->sortBy('path')
            ->values()
            ->toArray();

        return response()->json([
            'totalCount' => $total_count,
            'incompleteResults' => $incomplete_results,
            'items' => $cssFiles
        ]);
    }
}
