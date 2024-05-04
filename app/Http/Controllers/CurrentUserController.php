<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CurrentUserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(UserResource::make(Auth::user()));
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

    public function destroy(): JsonResponse
    {
//        Auth::user()->delete();
//        Auth::logout();
        return response()->json(null, 204);
    }

}
