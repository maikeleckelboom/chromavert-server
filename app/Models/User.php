<?php

namespace App\Models;

use App\HasProfilePhoto;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;
use TaylorNetwork\UsernameGenerator\GeneratesUsernames;

/**
 * @method static create(array $data)
 * @method static findOrFail($id)
 * @method static firstOrNew(array $array)
 * @method static find(int $id)
 * @property int $id
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory;
    use Notifiable;
    use HasProfilePhoto;
    use FindSimilarUsernames;
    use GeneratesUsernames;
    use SoftDeletes;
    use HasRoles;


    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * @return HasMany
     */
    public function identityProviders(): HasMany
    {
        return $this->hasMany(IdentityProvider::class);
    }

    public function hasProvider(string $provider, string $providerUserId): bool
    {
        return $this->identityProviders()
            ->where('provider', $provider)
            ->where('provider_user_id', $providerUserId)
            ->exists();
    }
}
