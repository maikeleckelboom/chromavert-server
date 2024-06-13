<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class SearchGithubRepoController extends Controller
{
    static string $GithubSearchUrl = 'https://api.github.com/search';

    /**
     * @throws ConnectionException
     */
    public function __invoke(Request $request, $repo): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $extensions = ['css', 'scss', 'sass', 'less', 'pcss'];

        $query = "repo:{$github->provider_user_nickname}/{$repo}+extension:" . implode('+extension:', $extensions);

        $response = Http::withToken($github->token)->get(self::$GithubSearchUrl . "/code?q={$query}")->json();

        $response['items'] = $this->sortAndMerge($response['items']);

        return response()->json($response);

    }

    private function sortAndMerge(array $source): array
    {
        $response = collect($source);
        $response = $response->sortBy('path');
        return $response->values()->toArray();
    }
}
