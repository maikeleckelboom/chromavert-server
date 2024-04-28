<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\AuthProviderService;
use App\Models\AuthProvider;
use App\Models\User;
use App\Providers\RedirectRouteProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class AuthProviderController extends Controller
{
    private bool $preventAccountLockout = false;

    public function index(AuthProviderService $providerService): JsonResponse
    {
        $providers = $providerService->all();
        return response()->json($providers);
    }

    public function disconnect(AuthProviderService $providerService, $id): JsonResponse
    {
        $providerUser = $providerService->find($id)->user;
        $user = User::findOrFail($providerUser->user_id);

        if ($user->id !== auth()->id()) {
            return response()->json(['message' => 'unauthorized'], 403);
        }

        if ($this->preventAccountLockout && $this->willBeLockedOut($user)) {
            return response()->json([
                'cause' => 'cannot-disconnect-last-provider',
                'message' => 'You cannot disconnect the last provider. Create a password to continue.',
            ], 400);
        }

        if ($providerService->disconnect($id)) {
            return response()->json([
                'cause' => 'has-disconnected-provider',
                'message' => 'The provider has been disconnected.',
            ], 204);
        };

        return response()->json(['message' => 'provider-not-found'], 404);
    }

    public function redirect(string $provider): RedirectResponse
    {
        if (!Auth::check()) {
            return Socialite::driver($provider)->redirect();
        }

        $params = $this->getParamsFromRequest(request());
        return Socialite::driver($provider)->with($params)->redirect();
    }

    public function callback(AuthProviderService $providerService, $provider): RedirectResponse
    {
        $SPA_URL = config('app.frontend_url');

        try {
            $user = Socialite::driver($provider)->user();
        } catch (InvalidStateException $e) {
            $url = $SPA_URL . RedirectRouteProvider::getRoute(Auth::check() ? 'onAuthOnly' : 'onGuestOnly');
            $error = request()->has('error')
                ? '&error=' . urlencode($e->getMessage() ?? 'invalid-state')
                : '';
            return redirect()->to($url . $error);
        }

        $authenticatableUser = $providerService->findOrCreate($user, $provider);

        if (!Auth::check()) {
            auth()->login($authenticatableUser, true);
        }

        $authenticatableUser->touch();

        $redirectUrl = $SPA_URL . RedirectRouteProvider::getRoute('onLogin');

        return redirect()->to($redirectUrl);
    }

    private function getParamsFromRequest($request): array
    {
        $params = [];
        if ($request->has('email')) {
            $params['login_hint'] = $request->get('email');
        } else {
            $params['prompt'] = 'select_account';
        }
        return $params;
    }


    private function isLastProvider(): bool
    {
        return AuthProvider::where('user_id', auth()->id())->count() === 1;
    }

    private function willBeLockedOut($user): bool
    {
        return $user->isPasswordNull() && $this->isLastProvider();
    }
}
