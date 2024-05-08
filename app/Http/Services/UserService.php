<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;


class UserService
{

    public function getCurrentUser(): User
    {
        return $this->getUserById(auth()->id());
    }

    public function getUserById(int $id): User
    {
        return User::findOrFail($id);
    }

    public function updateUserById(int $id, array $input): array
    {
        $user = $this->getUserById($id);

        DB::transaction(function () use ($user, $input) {
            if (isset($input['avatar'])) {
                $user->updateAvatar($input['avatar']);
            }

            $this->forceUpdate($user, collect($input)->except('avatar')->toArray());
        });

        return $user->refresh()->getChanges();
    }

    protected function forceUpdate(User $user, array $input): void
    {
        $currentAttributes = $user->getAttributes();

        $filteredInput = array_filter($input, function ($value, $key) use ($currentAttributes) {
            return $value !== null && (!isset($currentAttributes[$key]) || $value !== $currentAttributes[$key]);
        }, ARRAY_FILTER_USE_BOTH);

        $user->forceFill(array_merge(
            $currentAttributes,
            $filteredInput
        ))->save();
    }

    public function tryUpdatePrimaryEmailAddress(int $id, string $email): mixed
    {
        $user = $this->getUserById($id);
        $userEmail = $user->email;
        $providerEmails = $user->authProviders()->where('provider_user_email', $email)->get();
        if ($this->isRegisteredWithProvider($providerEmails, $email) || ($userEmail === $email && $user->email_verified_at !== null)) {
            return $user->update(['email' => $email]);
        }
        return false;
    }


    public function isRegisteredWithProvider(Collection $providers, string $email): bool
    {
        return $providers->some(fn($provider) => $provider->email === $email);
    }
}

