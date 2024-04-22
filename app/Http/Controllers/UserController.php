<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function index(): AnonymousResourceCollection
    {
        $users = $this->userService->all();
        return UserResource::collection($users);
    }

    public function store(StoreUserRequest $request): UserResource
    {

        $data = $request->validated();
        $user = $this->userService->create($data);
        return UserResource::make($user);
    }

    public function show($id): UserResource
    {
        $user = $this->userService->find($id);
        return UserResource::make($user);
    }

    public function update(UpdateUserRequest $request, $id): UserResource
    {
        $data = $request->validated();
        $user = $this->userService->update($data, $id);
        return UserResource::make($user);
    }

    public function destroy($id): JsonResponse
    {
        $this->userService->delete($id);
        return response()->json([], ResponseAlias::HTTP_NO_CONTENT);
    }
}
