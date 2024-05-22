<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Services\IdentityProviderService;
use App\Http\Services\UserService;
use App\Models\IdentityProvider;
use App\Models\User;
use App\Providers\AuthRedirectProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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

    public function disconnect(IdentityProviderService $providerService, $id): JsonResponse
    {
        $providerUser = $providerService->findProviderForAuthUser($id);
        $user = User::findOrFail($providerUser->id);

        if ($user->id !== auth()->id()) {
            return response()->json([
                'message' => "You cannot disconnect other users' providers.",
                'description' => 'The provider does not belong to the authenticated user.',
            ], 403);
        }

        if ($this->willBeLockedOut($user)) {
            return response()->json([
                'message' => 'You cannot disconnect the last provider.',
                'description' => 'You will be locked out if you disconnect this provider.',
            ], 400);
        }

        if ($providerService->disconnect($id)) {
            return response()->json([
                'message' => 'The provider has been disconnected.',
                'description' => 'The provider has been successfully disconnected.',
            ], 204);
        };

        return response()->json([
            'message' => 'The provider was not found.',
            'description' => 'The provider does not exist.',
        ], 404);
    }

    public function redirect(string $provider): RedirectResponse
    {
        if (!Auth::check()) return Socialite::driver($provider)->redirect();
        $params = $this->getRequestParams(request());
        return Socialite::driver($provider)->with($params)->redirect();
    }

    public function callback(IdentityProviderService $providerService, $provider): RedirectResponse
    {
        $SPA_URL = config('app.frontend_url');

        try {
            $providerUser = Socialite::driver($provider)->user();
            $authenticatableUser = $providerService->findOrCreate($providerUser, $provider);
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



    private function getRequestParams($request): array
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
