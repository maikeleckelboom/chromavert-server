<?php

namespace App\Http\Services;

use App\Http\Resources\AuthProviderResource;
use App\Models\AuthProvider;
use App\Models\User;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as ProviderUser;
use TaylorNetwork\UsernameGenerator\Facades\UsernameGenerator;

class AuthProviderService
{
    public function all(): AnonymousResourceCollection
    {
        $user = User::findOrFail(auth()->id());
        return AuthProviderResource::collection($user->authProviders);
    }

    protected function find($id)
    {
        return AuthProvider::findOrFail($id);
    }

    public function findOrCreate(ProviderUser $providerUser, $provider): User
    {

        // If the user is already authenticated, connect the provider
        if (Auth::check()) {
            $user = User::findOrFail(auth()->id());
            $this->connect($user, $providerUser, $provider);
            return $user;
        }

        // Find or create the provider
        $authProvider = $this->firstOrNew($provider, $providerUser);

        // If the provider exists, return the user
        if ($authProvider->exists) {
            return User::findOrFail($authProvider->user_id);
        }

        // Start by finding the user by email
        $user = User::firstOrNew([
            'email' => $providerUser->getEmail(),
        ]);

        // If the user does not exist, create it
        if (!$user->exists) {
            $user = $this->inheritUserAttributes($user, $providerUser);
            $user->save();
        }

        // Associate the user with the provider
        $authProvider->user()->associate($user);
        $authProvider->save();

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

    private function connect(User $user, ProviderUser $providerUser, $provider): bool
    {
        $authProvider = $this->firstOrNew($provider, $providerUser);
        $authProvider->user()->associate($user);
        if ($authProvider->save()) {
            $authProvider->touch();
            return true;
        }

        return false;
    }

    private function inheritUserAttributes(User $user, ProviderUser $providerUser): User
    {
        $user->username ??= UsernameGenerator::generate(
            $providerUser->getNickname() ?? $providerUser->getName()
        );
        $user->email ??= $providerUser->getEmail();
        $user->name ??= $providerUser->getName();
        $user->avatar ??= $providerUser->getAvatar();
        return $user;
    }
}
