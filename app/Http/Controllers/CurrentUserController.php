<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrentUserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(UserResource::make($request->user()));
    }
}
