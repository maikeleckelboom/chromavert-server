<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IdentityProviderResource extends JsonResource
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
            'name' => $this->user_name,
            'email' => $this->user_email,
            'username' => $this->user_nickname,
            'avatar' => $this->user_avatar,
            'token' => $this->token,
            'approvedScopes' => $this->approved_scopes,
            'refreshToken' => $this->refresh_token,
            'expiresAt' => $this->expires_at,
            'createdAt' => $this->created_at->format('d M Y H:i'),
            'updatedAt' => $this->updated_at->diffForHumans(),
        ];
    }
}
