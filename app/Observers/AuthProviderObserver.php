<?php

namespace App\Observers;

use App\Http\Services\UserService;
use App\Models\AuthProvider;
use App\Models\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class AuthProviderObserver
{
    public function deleted(AuthProvider $authProvider, UserService $userService): void
    {
        $user = User::findOrFail($authProvider->user_id);

        if ($this->isLockedOut($user)) {
            $userService->deleteUser($user);
        }
    }

    private function isLockedOut($user): bool
    {
        return !is_null($user) && is_null($user->password) && $user->authProviders->count() === 0;
    }
}
