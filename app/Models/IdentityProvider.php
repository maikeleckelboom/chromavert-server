<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static firstOrNew(array $array)
 * @method static findOrFail($id)
 * @method static where(string $string, mixed $input)
 */
class IdentityProvider extends Model
{
    use HasFactory;

    protected $fillable = [
        'provider',
        'provider_user_id',
        'provider_user_email',
        'provider_user_name',
        'provider_user_nickname',
        'provider_user_avatar',
        'token',
        'approved_scopes',
        'refresh_token',
        'expires_at',
    ];

    protected $casts = [
        'approved_scopes' => 'array',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provider(): Attribute
    {
        return new Attribute(
            get: fn($value, $attributes) => ucfirst($attributes['provider']),
            set: fn($value) => ['provider' => strtolower($value)]
        );
    }

}
