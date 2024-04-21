<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    )
    {
    }

    public function index(): AnonymousResourceCollection
    {
        $users = $this->userService->all();
        return UserResource::collection($users);
    }

    public function store(Request $request): UserResource
    {
        $data = $request->validate([
            'name' => 'required|min:2|max:255',
            'email' => 'required|unique:users,email',
            'password' => 'required|confirmed',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $user = $this->userService->create($data);

        return UserResource::make($user);
    }

    public function show($id): UserResource
    {
        $user = $this->userService->find($id);
        return UserResource::make($user);
    }

    public function edit($id): UserResource
    {
        $user = $this->userService->find($id);
        return UserResource::make($user);
    }

    public function update(Request $request, $id): UserResource
    {
        $data = $request->validate([
            'name' => 'sometimes|min:2|max:255',
            'email' => 'sometimes|unique:users,email,' . $id,
            'password' => 'sometimes|confirmed',
            'avatar' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        $user = $this->userService->update($data, $id);
        return UserResource::make($user);
    }

    public function destroy($id): JsonResponse
    {
        $this->userService->delete($id);
        return response()->json([], ResponseAlias::HTTP_NO_CONTENT);
    }
}
