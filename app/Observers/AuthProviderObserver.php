<?php

namespace App\Observers;

use App\Models\AuthProvider;
use App\Models\User;

class AuthProviderObserver
{
    public function created(AuthProvider $authProvider): void
    {

    }

    public function updated(AuthProvider $authProvider): void
    {
        //
    }

    public function deleted(AuthProvider $authProvider): void
    {
        $user = User::findOrFail($authProvider->user_id);
        if ($this->isLockedOut($user)) {
            $user->delete();
        }
    }

    public function restored(AuthProvider $authProvider): void
    {
        //
    }

    public function forceDeleted(AuthProvider $authProvider): void
    {
        //
    }

    private function isLockedOut($user): bool
    {
        return !is_null($user)
            && $user->password === null
            && $user->authProviders->count() === 0;
    }
}
