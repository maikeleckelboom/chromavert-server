<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Validator;
use TaylorNetwork\UsernameGenerator\Generator;


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

    public function deleteUser(User $user): void
    {
        $user->authProviders()->delete();
        $user->deleteProfilePhoto();
        $user->forceDelete();
    }

    public function updatePassword(User $user, array $input): void
    {
        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }

    public function updateProfileInformation(User $user, array $input): void
    {
        if (isset($input['__delete_photo'])) {
            $user->deleteProfilePhoto();
        } else if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['username'] !== $user->username) {
            $input['username'] = $this->generateUsername($input['username']);
        }

        if ($input['email'] !== $user->email && $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'username' => $input['username'],
                'email' => $input['email'],
            ])->save();
        }
    }

    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'username' => $input['username'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }

    private function generateUsername(string $username): string
    {
        return (new Generator())->generate($username);
    }
}

