<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use TaylorNetwork\UsernameGenerator\Generator;

class UserProfileController
{

    /**
     * @throws ValidationException
     */
    public function update(Request $request): JsonResponse
    {
        $input = $request->all();
        $user = $request->user();

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['nullable', 'mimes:jpg,jpeg,png,svg,avif,gif,webp', 'max:1024']
        ])->validateWithBag('updateProfileInformation');

        if (isset($input['photo'])) {
            $user->updateProfilePhoto($input['photo']);
        }

        if ($input['username'] !== $user->username) {
            $input['username'] = (new Generator())->generate($input['username']);
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

        return response()->json([
            'message' => 'Update Successful',
            'description' => 'Your profile information has been updated.',
        ]);
    }

    protected function getType($input): string
    {
        return gettype($input);
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param array<string, string> $input
     */
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

    public function destroy(Request $request): JsonResponse
    {
        $request->user()->deleteProfilePhoto();

        return response()->json([
            'message' => 'Deletion Successful',
            'description' => 'Your profile photo has been removed.',
        ]);
    }

}
