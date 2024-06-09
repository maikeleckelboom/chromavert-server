<?php

namespace App\Http\Controllers\Github;

use App\Http\Controllers\Controller;
use App\Models\IdentityProvider;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RepoController extends Controller
{

    public function redirect(): RedirectResponse
    {
        return Socialite::driver('github')->scopes(['repo'])->redirect();
    }

    public function callback(): RedirectResponse
    {
        $user = Socialite::driver('github')->user();

        $identityProvider = IdentityProvider::firstOrNew([
            'provider' => 'github',
            'provider_user_id' => $user->getId(),
        ]);

        $identityProvider->fill([
            'provider_user_email' => $user->getEmail(),
            'provider_user_name' => $user->getName(),
            'provider_user_nickname' => $user->getNickname(),
            'provider_user_avatar' => $user->getAvatar(),
            'token' => $user->token,
            'refresh_token' => $user->refreshToken,
            'expires_at' => now()->addSeconds($user->expiresIn),
        ]);

        $identityProvider->user()->associate(auth()->user());
        $identityProvider->save();

        return redirect()->to(config('app.frontend_url') . '/github');
    }

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

        $sortedResponse = $this->sortByMostRecent($response->json());

        return response()->json($sortedResponse);
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

        $repo = $response->json();

        return response()->json($repo);
    }

    private function sortByMostRecent(array $repos): array
    {
        usort($repos, function ($a, $b) {
            return strtotime($b['updated_at']) - strtotime($a['updated_at']);
        });

        return $repos;
    }
}
