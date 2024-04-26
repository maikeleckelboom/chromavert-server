<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthProviderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'provider' => $this->provider,
            'user' => UserResource::make([
                'email' => $this->provider_user_email,
                'name' => $this->provider_user_name,
                'username' => $this->provider_user_nickname,
                'avatar' => $this->provider_user_avatar,
            ]),
            'createdAt' => $this->created_at->format('d M Y H:i'),
            'updatedAt' => $this->updated_at->diffForHumans(),
        ];
    }
}
