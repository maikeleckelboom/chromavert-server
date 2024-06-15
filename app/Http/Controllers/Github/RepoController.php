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
        $github = $request->user()->identityProviders()->where('provider', 'github')->first();

        $fetcher = Http::withToken($github->token);

        $response = $fetcher->get('https://api.github.com/user/repos', [
            'per_page' => $request->get('per_page', 30),
            'page' => $request->get('page', 1),
            'sort' => $request->get('sort', 'updated')
        ]);

        $links = $response->header('Link') ?? null;

        return response()->json([
            'items' => $response->json(),
            'links' => $links ? $this->parseLinks($links) : null
        ]);
    }

    private function parseLinks(string $links): array
    {
        $links = explode(',', $links);
        $parsed = [];
        foreach ($links as $link) {
            preg_match('/<(.*)>; rel="(.*)"/', $link, $matches);
            $parsed[$matches[2]] = $matches[1];
        }
        return $parsed;
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

        return response()->json($response->json());
    }

}
