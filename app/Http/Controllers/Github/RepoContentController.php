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

        return response()->json($this->sortByFolderAndFile($response->json()));
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

        return response()->json($response->json());
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

        return response()->json($response->json());
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

        return response()->json($response->json());
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
}
