<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Services\IdentityProviderService;
use App\Models\IdentityProvider;
use App\Models\User;
use App\Providers\AuthRedirectProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class IdentityProviderController extends Controller
{
    public function index(IdentityProviderService $providerService): JsonResponse
    {
        $providers = $providerService->all();
        return response()->json($providers);
    }

    public function redirect(string $provider): RedirectResponse
    {
        return Socialite::driver($provider)
            ->scopes($this->getScopes($provider))
            ->with($this->getParams(request(), $provider))
            ->redirect();
    }

    public function callback(IdentityProviderService $providerService, $provider): RedirectResponse
    {
        $SPA_URL = config('app.frontend_url');

        try {
            $providerUser = Socialite::driver($provider)->user();
            $authenticatableUser = $providerService->retrieveUser($providerUser, $provider);
        } catch (InvalidStateException $e) {
            $error = '&error=' . request()->has('error') ? Str::slug($e->getMessage()) : 'invalid-state';
            $path = AuthRedirectProvider::getRoute(Auth::check() ? 'onAuthOnly' : 'onGuestOnly');
            return redirect()->to($SPA_URL . $path . $error);
        }

        if (!Auth::check()) {
            auth()->login($authenticatableUser, true);
        }

        $authenticatableUser->touch();
        $redirectUrl = $SPA_URL . AuthRedirectProvider::getRoute('onLogin');
        return redirect()->to($redirectUrl);
    }

    public function disconnect(IdentityProviderService $providerService, $id): JsonResponse
    {
        $identityProvider = $providerService->findProviderForAuthUser($id);
        $user = User::findOrFail($identityProvider->user->id);

        if ($user->id !== auth()->id()) {
            abort(403, 'You are not authorized to disconnect this provider.');
        }

        if ($this->willBeLockedOut($user)) {
            return response()->json(['message' => 'You cannot disconnect the last provider.'], 400);
        }

        if ($providerService->disconnect($id)) {
            return response()->json(['message' => 'The provider has been disconnected.'], 204);
        };

        return response()->json(['message' => 'The provider could not be disconnected.'], 400);
    }


    private function getScopes($provider): array
    {
        return match ($provider) {
            'github' => ['repo'],
            default => [],
        };
    }

    private function getParams(Request $request, $provider): array
    {
        return match ($provider) {
            'google' => $this->getGoogleParams($request),
            default => [],
        };
    }

    private function getGoogleParams($request): array
    {
        return $request->has('email')
            ? ['login_hint' => $request->get('email')]
            : ['prompt' => 'select_account'];
    }

    private function isLastProvider(): bool
    {
        return IdentityProvider::where('user_id', auth()->id())->count() === 1;
    }

    private function willBeLockedOut($user): bool
    {
        return is_null($user->password) && $this->isLastProvider();
    }
}
