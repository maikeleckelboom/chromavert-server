<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\UserResource;
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
            'name' => $this->provider,
            'createdAt' => $this->created_at->format('d M Y H:i'),
            'updatedAt' => $this->updated_at->diffForHumans(),
            'user' => UserResource::make([
                'email' => $this->provider_user_email,
                'name' => $this->provider_user_name,
                'username' => $this->provider_user_nickname,
                'avatar' => $this->provider_user_avatar,
            ]),
        ];
    }
}
