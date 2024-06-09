<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RepoContentController extends Controller
{
    public function index(Request $request, $name): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $response = Http::withToken($github->token)
            ->get("https://api.github.com/repos/{$github->provider_user_nickname}/{$name}/contents");

        $sortedResponse = $this->sort($response->json());

        return response()->json($sortedResponse);
    }

    public function show(Request $request, $name, $fileOrFolder): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $response = Http::withToken($github->token)
            ->get("https://api.github.com/repos/{$github->provider_user_nickname}/{$name}/contents/{$fileOrFolder}");

        $sortedResponse = $this->sort($response->json());

        return response()->json($sortedResponse);
    }

    public function path(Request $request, $name, $path): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $response = Http::withToken($github->token)
            ->get("https://api.github.com/repos/{$github->provider_user_nickname}/{$name}/contents/{$path}");

        $sortedResponse = $this->sort($response->json());

        return response()->json($sortedResponse);
    }

    public function branches(Request $request, $repo): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $response = Http::withToken($github->token)
            ->get("https://api.github.com/repos/{$github->provider_user_nickname}/{$repo}/branches");

        $sortedResponse = $this->sort($response->json());

        return response()->json($sortedResponse);
    }

    private function sort(array $repos): array
    {
        return $this->sortByFolderAndFile($this->sortAbc($repos));
    }

    private function sortByFolderAndFile(array $repos): array
    {
        $folders = [];
        $files = [];

        foreach ($repos as $repo) {
            if ($repo['type'] === 'dir') {
                $folders[] = $repo;
            } else {
                $files[] = $repo;
            }
        }

        return array_merge($folders, $files);
    }

    private function sortAbc(array $repos): array
    {
        usort($repos, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $repos;
    }
}
