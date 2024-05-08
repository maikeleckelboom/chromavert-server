<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CurrentUserController extends Controller
{
    public function index(Request $request, UserService $userService): JsonResponse
    {
        $user = $userService->getCurrentUser();
        return response()->json($user);
    }

    public function account(Request $request, UserService $userService): JsonResponse
    {
        $user = $userService->getCurrentUser();

        $result = [
            'name' => $user->getNames(),
            'email' => $user->getEmailAddresses(),
            'avatar' => $user->getAvatarsList(),
            'username' => $user->getUsernames(),
        ];

        $jsonResponse =
            !empty($request->all())
                ? array_filter($result, fn($key) => $request->has($key), ARRAY_FILTER_USE_KEY)
                : $result;

        return response()->json($jsonResponse);
    }

    public function update(UpdateUserRequest $request, UserService $userService): JsonResponse
    {
        try {
            $changes = $userService->updateUserById(Auth::id(), $request->validated());
            return response()->json($changes, ResponseAlias::HTTP_OK);
        } catch (\Exception $e) {
            return response()->json([
                'cause' => 'database-error',
                'exception' => $e->getMessage(),
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateEmail(Request $request, UserService $userService): JsonResponse
    {
        $email = $request->input('email');
        $user = $userService->getCurrentUser();
        $success = $userService->tryUpdatePrimaryEmailAddress($user->id, $email);
        if($success) {
            return response()->json(null, ResponseAlias::HTTP_NO_CONTENT);
        }
        return response()->json([
            'message' => 'Email is already in use',
        ], ResponseAlias::HTTP_CONFLICT);

    }

    public function destroy(): JsonResponse
    {
//        Auth::user()->delete();
//        Auth::logout();
        return response()->json(null, 204);
    }
}
