<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RepoController extends Controller
{
    /**
     * @throws ConnectionException
     */
    public function index(Request $request): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $response = Http::withToken($github->token)
            ->get('https://api.github.com/user/repos');

        $repos = $this->sort($response->json());

        return response()->json($repos);
    }

    public function show(Request $request, $name): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $response = Http::withToken($github->token)
            ->get("https://api.github.com/repos/{$github->provider_user_nickname}/{$name}");

        $repo = $this->sort($response->json());

        return response()->json($repo);
    }

    private function sort(array $repos): array
    {
        usort($repos, fn($a, $b) => strtotime($b['updated_at']) - strtotime($a['updated_at']));
        return $repos;
    }
}
