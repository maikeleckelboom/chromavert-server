<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RepoContentController extends Controller
{
    static string $GithubReposUrl = 'https://api.github.com/repos';

    /**
     * @throws ConnectionException
     */
    public function paths(Request $request, $repo, ...$paths): JsonResponse
    {
        $github = $request->user()->identityProviders()->where('provider', 'github')->first();

        $branch = $request->get('ref');
        $username = $github->provider_user_nickname;
        $path = implode('/', $paths) ?: null;

        $response = Http::withToken($github->token)->get(self::$GithubReposUrl . "/{$username}/{$repo}/contents" . ($path ? "/{$path}" : "") . ($branch ? "?ref={$branch}" : ""))->json();

        if ($this->isFilePath($paths)) {
            $response = collect($response);

            if ($this->isFile($response->get('content'))) {
                return response()->json([
                    'type' => 'file',
                    'content' => $response->get('content')
                ]);
            }

            $content = base64_decode($response->get('content'));
            return response()->json($response->merge(['content' => $content])->toArray());
        }

        return response()->json($this->separateFolderAndFile($response));
    }

    private function isFilePath($paths): bool
    {
        return str_contains(end($paths), '.');
    }

    private function isFile(string $content): bool
    {
        return str_contains($content, 'data:');
    }

    private function separateFolderAndFile(array $repos): array
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

    public function branches(Request $request, $repo): JsonResponse
    {
        $github = $request->user()->identityProviders()->where('provider', 'github')->first();

        $response = Http::withToken($github->token)->get(self::$GithubReposUrl . "/{$github->provider_user_nickname}/{$repo}/branches");


        return response()->json($response->json());
    }

    public function commits(Request $request, $repo): JsonResponse
    {
        $github = $request->user()->identityProviders()->where('provider', 'github')->first();

        $response = Http::withToken($github->token)->get(self::$GithubReposUrl . "/{$github->provider_user_nickname}/{$repo}/commits");

        return response()->json($response->json());
    }
}
