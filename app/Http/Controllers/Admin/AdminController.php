<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        return UserResource::collection(User::all());
    }
}
