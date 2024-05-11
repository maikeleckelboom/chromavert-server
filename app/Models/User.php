<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;
use TaylorNetwork\UsernameGenerator\GeneratesUsernames;
use App\Traits\HasProfilePhoto;

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

    private string|null $password;


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
    public function authProviders(): HasMany
    {
        return $this->hasMany(AuthProvider::class);
    }

    /**
     * @return bool
     */
    public function isEmailVerified(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function isPasswordNull(): bool
    {
        return is_null($this->password);
    }

    public static function checkIfPasswordNull(User $user): bool
    {
        return is_null($user->password);
    }
}
