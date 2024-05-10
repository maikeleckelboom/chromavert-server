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

        $this->forceUpdate($user, $input);

        return $user->getChanges();
    }

    private function forceUpdate(User $user, array $input): void
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

