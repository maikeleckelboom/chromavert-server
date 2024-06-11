<?php

namespace App\Http\Services;

class GithubService
{
    public function getBaseUrl(): string
    {
        return 'https://api.github.com/repos/';
    }
}
