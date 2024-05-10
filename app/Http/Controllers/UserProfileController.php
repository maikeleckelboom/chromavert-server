<?php

namespace App\Http\Controllers;

use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class UserProfileController
{

    /**
     * @throws ValidationException
     */
    public function update(Request $request, UserService $userService): JsonResponse
    {
        $user = $request->user();

        $input = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'photo' => ['mimes:jpg,jpeg,png,svg,avif,gif', 'max:1024'],
        ])->validateWithBag('updateProfileInformation');

        if ($request->only('__delete_photo')) {
            $input['__delete_photo'] = true;
        }

        DB::transaction(fn() => $userService->updateProfileInformation($user, $input));

        return response()->json([
            'message' => 'Update Successful',
            'description' => 'Your profile information has been updated.',
        ]);
    }

    public function deleteProfilePhoto(Request $request): JsonResponse
    {
        $request->user()->deleteProfilePhoto();

        return response()->json([
            'message' => 'Deletion Successful',
            'description' => 'Your profile photo has been removed.',
        ]);
    }
}
