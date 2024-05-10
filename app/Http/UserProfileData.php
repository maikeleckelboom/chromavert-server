<?php

namespace App\Http;

class UserProfileData
{
    public function __construct(
        public string      $name,
        public string      $email,
        public ?string     $username,
        public string|null $photo
    )
    {
    }
}
