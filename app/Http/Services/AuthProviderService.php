<?php

namespace App\Http\Services;

use App\Http\Resources\Auth\AuthProviderResource;
use App\Models\AuthProvider;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as ProviderUser;
use TaylorNetwork\UsernameGenerator\Facades\UsernameGenerator;

class AuthProviderService
{
    public function all(): Collection
    {
        $user = User::findOrFail(auth()->id());
        return collect($user->authProviders)
            ->map(fn($provider) => new AuthProviderResource($provider));
    }

    public function find($id)
    {
        return AuthProvider::where('user_id', auth()->id())->findOrFail($id);
    }

    public function findOrCreate(ProviderUser $providerUser, $provider): User
    {
        if (Auth::check()) {
            $user = User::findOrFail(auth()->id());
            $this->connect($user, $providerUser, $provider);
            return $user;
        }

        $authProvider = $this->firstOrNew($provider, $providerUser);

        if ($authProvider->exists) {
            return $authProvider->user;
        }

        $user = User::firstOrNew([
            'email' => $providerUser->getEmail(),
        ]);

        if (!$user->exists) {
            $user = $this->inheritMissingAttributes($user, $providerUser);
            $user->save();

            event(new Registered($user));
        }

        $authProvider->user()->associate($user)->save();

        return $user;
    }

    public function firstOrNew(string $provider, ProviderUser $providerUser): AuthProvider
    {
        return AuthProvider::firstOrNew([
            'provider' => $provider,
            'provider_user_id' => $providerUser->getId(),
            'provider_user_name' => $providerUser->getName(),
            'provider_user_nickname' => $providerUser->getNickname(),
            'provider_user_avatar' => $providerUser->getAvatar(),
            'provider_user_email' => $providerUser->getEmail(),
        ]);
    }

    public function disconnect($id): bool
    {
        $authProvider = AuthProvider::findOrFail($id);
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
        return $user;
    }
}
