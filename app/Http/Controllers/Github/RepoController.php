<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
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

        if (!is_null($links)) {
            $links = explode(',', $links);
            $links = collect($links)->mapWithKeys(function ($link) {
                preg_match('/<(.*)>; rel="(.*)"/', $link, $matches);
                return [$matches[2] => $matches[1]];
            })->toArray();
        }

        return response()->json([
            'data' => $response->json(),
            'pagination' => $links
        ]);
    }

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
