<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RepoContentController extends Controller
{
    static string $api = 'https://api.github.com/repos';

    public function repo(Request $request, $repo): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $response = Http::withToken($github->token)
            ->get(self::$api . "/{$github->provider_user_nickname}/{$repo}");

        return response()->json($response->json());
    }

    public function repoContents(Request $request, $name): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        // Have to replace $github->provider_user_nickname with real owner name
        // Because the owner name is not always the same as the authenticated user
        // For example, the authenticated user is a collaborator.
        $response = Http::withToken($github->token)
            ->get(self::$api . "/{$github->provider_user_nickname}/{$name}/contents")
            ->json();


        return response()->json($this->sort($response));
    }

    public function paths(Request $request, $repo, ...$paths): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $branch = $request->get('ref');
        $username = $github->provider_user_nickname;
        $path = implode('/', $paths) ?: null;

        $response = Http::withToken($github->token)
            ->get(
                self::$api
                . "/{$username}/{$repo}/contents"
                . ($path ? "/{$path}" : "")
                . ($branch ? "?ref={$branch}" : "")
            )
            ->json();


        if ($this->isFile($paths)) {
            $response = collect($response);
            $content = base64_decode($response->get('content'));
            return response()->json($response->merge(['content' => $content])->toArray());
        }

        return response()->json($this->sort($response));
    }

    public function branches(Request $request, $repo): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $response = Http::withToken($github->token)
            ->get(self::$api . "/{$github->provider_user_nickname}/{$repo}/branches");


        return response()->json($response->json());
    }

    public function commits(Request $request, $repo): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $response = Http::withToken($github->token)
            ->get(self::$api . "/{$github->provider_user_nickname}/{$repo}/commits");

        return response()->json($response->json());
    }

    private function isFile($paths): bool
    {
        return str_contains(end($paths), '.');
    }

    private function sort(array $repos): array
    {
        return $this->separateFolderAndFile(
            $this->sortByMostRecent($repos)
        );
    }

    protected function separateFolderAndFile(array $repos): array
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

    protected function sortByMostRecent(array $repos): array
    {
        usort($repos, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $repos;
    }
}
