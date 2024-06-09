<?php

namespace App\Http\Services;

use App\Http\Resources\IdentityProviderResource;
use App\Models\IdentityProvider;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as ProviderUser;
use TaylorNetwork\UsernameGenerator\Facades\UsernameGenerator;

class IdentityProviderService
{
    public function all(): Collection
    {
        $user = User::findOrFail(auth()->id());
        return collect($user->identityProviders)->map(fn($provider) => new IdentityProviderResource($provider));
    }

    public function findProviderForAuthUser($id)
    {
        return IdentityProvider::where('user_id', auth()->id())->findOrFail($id);
    }

    public function findOrCreate(ProviderUser $providerUser, $provider): User
    {
        if (Auth::check()) {
            $user = User::findOrFail(auth()->id());
            $this->connect($user, $providerUser, $provider);
            return $user;
        }

        $identityProvider = $this->firstOrNew($provider, $providerUser);

        if ($identityProvider->exists) return $identityProvider->user;

        $user = User::firstOrNew(['email' => $providerUser->getEmail()]);

        if (!$user->exists) {
            $user = $this->inheritMissingAttributes($user, $providerUser);
            event(new Registered($user));
        }

        $identityProvider->user()->associate($user)->save();

        return $user;
    }

    public function firstOrNew(string $provider, ProviderUser $providerUser): IdentityProvider
    {
        // todo: look at github response for approved scopes
        return IdentityProvider::firstOrNew([
            'provider' => $provider,
            'provider_user_id' => $providerUser->getId(),
            'provider_user_name' => $providerUser->getName(),
            'provider_user_nickname' => $providerUser->getNickname(),
            'provider_user_avatar' => $providerUser->getAvatar(),
            'provider_user_email' => $providerUser->getEmail(),
            'token' => $providerUser->token,
            'approved_scopes' => $providerUser->getRaw()['approved_scopes'] ?? null,
            'refresh_token' => $providerUser->refreshToken,
            'expires_at' => $providerUser->expiresIn ? now()->addSeconds($providerUser->expiresIn) : null,
        ]);
    }

    public function disconnect($id): bool
    {
        $authProvider = IdentityProvider::findOrFail($id);
        return $authProvider->delete();
    }

    private function connect(User $user, ProviderUser $providerUser, $provider): void
    {
        $authProvider = $this->firstOrNew($provider, $providerUser);
        $authProvider->user()->associate($user)->save();
        if ($authProvider->save()) {
            $authProvider->touch();
        }
    }

    private function inheritMissingAttributes(User $user, ProviderUser $providerUser): User
    {
        $user->username ??= UsernameGenerator::generate(
            $providerUser->getNickname() ?? $providerUser->getName()
        );
        $user->email ??= $providerUser->getEmail();
        $user->name ??= $providerUser->getName();
        $user->profile_photo_path ??= $providerUser->getAvatar();
        $user->save();
        return $user;
    }
}
