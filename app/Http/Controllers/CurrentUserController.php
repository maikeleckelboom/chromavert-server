<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CurrentUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(UserResource::make($request->user()));
    }

    public function destroy(Request $request, UserService $userService): JsonResponse
    {
        DB::transaction(function () use ($request, $userService) {
            $userService->deleteUser($request->user());
        });

        return response()->json([
            'message' => 'Deletion Successful',
            'description' => 'Your account has been deleted. You will be logged out.',
        ]);
    }
}
