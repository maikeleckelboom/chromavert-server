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
class AuthProvider extends Model
{
    use HasFactory;

    public const SUPPORTED_PROVIDERS = [
        'google',
        'github',
    ];

    public User $user;

    protected $fillable = [
        'provider',
        'provider_user_id',
        'provider_user_email',
        'provider_user_name',
        'provider_user_nickname',
        'provider_user_avatar',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function provider(): Attribute
    {
        return new Attribute(
            get: fn ($value, $attributes) => ucfirst($attributes['provider']),
            set: fn ($value) => ['provider' => strtolower($value)]
        );
    }

}
