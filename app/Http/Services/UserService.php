<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\DB;


class UserService
{
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

            $this->updateUser($user, collect($input)->except('avatar')->toArray());
        });

        return $user->refresh()->getChanges();
    }

    protected function updateUser(User $user, array $input): void
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
}

