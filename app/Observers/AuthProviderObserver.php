<?php

namespace App\Observers;

use App\Models\AuthProvider;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class AuthProviderObserver
{
    public function deleted(AuthProvider $authProvider): void
    {
        $user = User::findOrFail($authProvider->user_id);

        if ($this->isLockedOut($user)) {
            $user->delete();
        }
    }

    private function isLockedOut($user): bool
    {
        return !is_null($user) && is_null($user->password) && $user->authProviders->count() === 0;
    }
}
