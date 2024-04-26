<?php

namespace App\Http;

class RegisteredUserData
{
    public function __construct(

        public string $email,
        public string $password,
        public ?string $name = null,
        public ?string $avatar = null,
        public ?string $username = null,

    )
    {
    }
}
