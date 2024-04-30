<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\RegisteredUserData;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class CurrentUserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(UserResource::make(Auth::user()), 200);
    }


    public function update(UpdateUserRequest $request): JsonResponse
    {
        $user = Auth::user();
        $validated = new RegisteredUserData(...$request->validated()->except('email'));
        $updated = DB::table('users')->where('id', Auth::id())->update([
            'name' => $validated->name,
            'username' => $validated->username,
            'avatar' => $validated->avatar,
        ]);

        if (!$updated) {
            return response()->json([
                'message' => 'An error occurred while updating the user. Please try again.',
                'cause' => 'database-error',
            ], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        };

        return response()->json(UserResource::make($user), 200);

    }

    public function destroy(): JsonResponse
    {

        return response()->json(null, 204);
    }

}
