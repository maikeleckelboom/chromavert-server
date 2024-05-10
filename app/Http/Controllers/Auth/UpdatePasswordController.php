<?php

namespace App\Http\Controllers\Auth;

use App\Http\Services\UserService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class UpdatePasswordController
{

    /**
     * Update user's password and notify.
     *
     * @param Request $request
     * @param UserService $userService
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, UserService $userService): JsonResponse
    {
        $user = $request->user();

        $userService->updatePassword(
            $user,
            $this->validatePasswordInput($user, $request->all())
        );

        return response()->json([
            'message' => 'Update Successful',
            'description' => 'Your password has been updated.',
        ]);
    }

    /**
     * Validate password change request input.
     *
     * @param User $user
     * @param array $input
     * @return array
     * @throws ValidationException
     */
    private function validatePasswordInput(User $user, array $input): array
    {
        if ($this->isPasswordNull($user)) {
            return Validator::make($input, [
                'password' => ['required', 'confirmed', Rules\Password::defaults()],
            ])->validateWithBag('updatePassword');
        }

        return Validator::make($input, [
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()]
        ])->after(fn($validator) => $this->checkCurrentPasswordMatches($user, $validator))
            ->validateWithBag('updatePassword');
    }

    /**
     * Validate if current password matches the user's stored password.
     *
     * @param User $user
     * @param $validator
     * @return void
     */
    private function checkCurrentPasswordMatches(User $user, $validator): void
    {
        $input = $validator->getData();
        if (!isset($input['current_password']) || !Hash::check($input['current_password'], $user->password)) {
            $validator->errors()->add('current_password', __('The provided password does not match your current password.'));
        }
    }

    /**
     * Check if user's password is null.
     *
     * @param User $user
     * @return bool
     */
    private function isPasswordNull(User $user): bool
    {
        return is_null($user->password);
    }
}
