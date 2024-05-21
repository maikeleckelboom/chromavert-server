<?php

namespace App\Http\Services;

use App\Http\Resources\IdentityProviderResource;
use App\Models\IdentityProvider;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as ProviderUser;
use Laravel\Socialite\Two\InvalidStateException;
use TaylorNetwork\UsernameGenerator\Facades\UsernameGenerator;

class IdentityProviderService
{
    public function all(): Collection
    {
        $user = User::findOrFail(auth()->id());
        return collect($user->identityProviders)->map(fn($provider) => new IdentityProviderResource($provider));
    }

    public function getProviderById($id)
    {
        return IdentityProvider::where('user_id', auth()->id())->findOrFail($id);
    }

    public function getAuthenticatableUser(ProviderUser $providerUser, string $provider): Authenticatable
    {
        $identityProvider = $this->firstOrNew($provider, $providerUser);

        if ($identityProvider->exists) {
            return $identityProvider->user;
        }

        $user = User::firstOrNew(['email' => $providerUser->getEmail()]);

        if(!$user->exists){
            $this->fillInMissingProperties($user, $providerUser);
        }

        $identityProvider->user()->associate($user)->save();
        event(new Registered($user));

        return $user;
    }

    public function firstOrNew(string $provider, ProviderUser $providerUser): IdentityProvider
    {
        return IdentityProvider::firstOrNew([
            'provider' => $provider,
            'provider_user_id' => $providerUser->getId(),
            'provider_user_name' => $providerUser->getName(),
            'provider_user_nickname' => $providerUser->getNickname(),
            'provider_user_avatar' => $providerUser->getAvatar(),
            'provider_user_email' => $providerUser->getEmail(),
//            'provider_token' => $providerUser->token,
//            'provider_refresh_token' => $providerUser->refreshToken,
//            'provider_expires_in' => $providerUser->expiresIn,
        ]);
    }

    public function disconnect($id): bool
    {
        $authProvider = IdentityProvider::findOrFail($id);
        return $authProvider->delete();
    }

    public function connect(User $user, ProviderUser $providerUser, $provider): void
    {
        $authProvider = $this->firstOrNew($provider, $providerUser);
        $authProvider->user()->associate($user)->save();
        if ($authProvider->save()) {
            $authProvider->touch();
        }
    }

    private function fillInMissingProperties(User $user, ProviderUser $providerUser): void
    {
        $user->username ??= UsernameGenerator::generate(
            $providerUser->getNickname() ?? $providerUser->getName()
        );
        $user->email ??= $providerUser->getEmail();
        $user->name ??= $providerUser->getName();
        $user->profile_photo_path ??= $providerUser->getAvatar();
        $user->save();
    }
}
