<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UpdateRepoContentController extends Controller
{
    public function __invoke(Request $request, $repo, $path): JsonResponse
    {
        $github = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $response = Http::withToken($github->token)
            ->put("https://api.github.com/repos/{$github->provider_user_nickname}/{$repo}/contents/{$path}", [
                'message' => $request->get('message'),
                'content' => base64_encode($request->get('content')),
                'sha' => $request->get('sha'),
                'branch' => $request->get('branch', 'main')
            ]);

        return response()->json($response->json());
    }
}
