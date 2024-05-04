<?php

namespace App\Http\Requests\Auth;

class RegisteredUserData
{
    public function __construct(

        public string $email,
        public string $password,
        public ?string $name = null,
        public ?string $username = null,
    )
    {
    }
}
