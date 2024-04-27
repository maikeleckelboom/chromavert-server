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
        'avatar' => "string|null",
        'emailVerified' => "bool",
        'createdAt' => "string",
        'updatedAt' => "string"
    ])] public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'avatar' => $this->avatar,
            'emailVerified' => $this->email_verified_at !== null,
            'createdAt' => $this->created_at->format('d M Y H:i'),
            'updatedAt' => $this->updated_at->diffForHumans(),
        ];
//        return parent::toArray($request);
    }
}
