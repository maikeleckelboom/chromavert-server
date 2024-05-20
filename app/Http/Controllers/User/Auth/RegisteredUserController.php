<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): Response
    {

        $validated = $request->validated();

        $user = User::create([
            'name' => $validated->name,
            'email' => $validated->email,
            'password' => Hash::make($validated->password),
        ]);

        event(new Registered($user));

        Auth::login($user, true);

        return response()->noContent();
    }
}
