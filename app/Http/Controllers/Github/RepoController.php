<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RepoController extends Controller
{

    public function index(Request $request): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $params = [
            'per_page' => $request->get('per_page', 30),
            'page' => $request->get('page', 1),
            'sort' => $request->get('sort', 'updated')
        ];

        $response = Http::withToken($github->token)
            ->get('https://api.github.com/user/repos', $params);

        $links = $response->header('Link') ?? null;

        return response()->json([
            'data' => $response->json(),
            'links' => $links ? $this->parseLinks($links) : null
        ]);
    }

    private function parseLinks(string $links): array
    {
        $links = explode(',', $links);
        $pagination = [];
        foreach ($links as $link) {
            $link = explode(';', $link);
            $url = $this->replaceUrl(trim($link[0], '<>'));
            $rel = trim($link[1], ' rel="');
            $pagination[$rel] = $url;
        }
        return $pagination;
    }

    private function replaceUrl(string $original, string $replace = ''): string
    {
        return str_replace('https://api.github.com/', $replace, $original);
    }

    /**
     * @throws ConnectionException
     */
    public function show(Request $request, $repo): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $response = Http::withToken($github->token)
            ->get("https://api.github.com/repos/{$github->provider_user_nickname}/{$repo}");

        if ($request->has('readme')) {
            $readme = $this->getREADME($github->provider_user_nickname, $repo, $github->token);

            return response()->json(
                array_merge($response->json(), ['readme' => base64_decode($readme)])
            );
        }

        return response()->json($response->json());
    }

    /**
     * @throws ConnectionException
     */
    private function getREADME(string $username, string $repo, string $token): string
    {
        return Http::withToken($token)
            ->get("https://api.github.com/repos/$username/$repo/readme")
            ->json()['content'];
    }
}
