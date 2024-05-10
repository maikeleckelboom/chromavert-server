<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape([
        'id' => "int",
        'name' => "string|null",
        'email' => "string",
        'username' => "string|null",
        'profilePhotoUrl' => "string|null",
        'emailVerified' => "bool",
        'password' => 'bool',
        'createdAt' => "string",
        'updatedAt' => "string",
        'photo' => 'string|null',
    ])] public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'profilePhotoUrl' => $this->profile_photo_url,
            'createdAt' => $this->created_at->format('d M Y H:i'),
            'updatedAt' => $this->updated_at->diffForHumans(),
            'emailVerified' => !is_null($this->email_verified_at),
            'password' => !is_null($this->password),
        ];
    }
}
