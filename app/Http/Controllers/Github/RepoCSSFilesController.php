<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RepoCSSFilesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, $repo): JsonResponse
    {
        $githubProvider = $request
            ->user()
            ->identityProviders()
            ->where('provider', 'github')
            ->first();

        $fetchService = Http::withToken($githubProvider->token);

        $cssFiles = $this->getCSSFiles($fetchService, $githubProvider->provider_user_nickname, $repo);

        return response()->json($cssFiles);
    }

    private function getCSSFiles($fetchService, $nickname, $repo, $path = '')
    {
        $response = $fetchService->get("https://api.github.com/repos/{$nickname}/{$repo}/contents/{$path}");

        $files = collect($response->json());

        $styleExtensions = ['css', 'scss', 'sass', 'less'];

        $cssFiles = $files
            ->filter(fn($file) => $file['type'] === 'file' && pathinfo($file['name'], PATHINFO_EXTENSION) === 'css')
            ->map(fn($file) => ['name' => $file['name'], 'path' => $file['path']])
            ->toArray();

        $directories = $files->filter(fn($file) => $file['type'] === 'dir');

        foreach ($directories as $dir) {
            $cssFiles = array_merge($cssFiles, $this->getCSSFiles($fetchService, $nickname, $repo, $dir['path']));
        }

        return $cssFiles;
    }
}
