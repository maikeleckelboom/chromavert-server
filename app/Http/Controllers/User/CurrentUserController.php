<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CurrentUserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(UserResource::make(Auth::user()), 200);
    }


    public function update(): JsonResponse
    {
        $user = Auth::user();
        $user->update(request()->all());
        return response()->json(UserResource::make($user), 200);
    }

    public function destroy(): JsonResponse
    {

        return response()->json(null, 204);
    }

}
