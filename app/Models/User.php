<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\HasAvatar;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use TaylorNetwork\UsernameGenerator\FindSimilarUsernames;
use TaylorNetwork\UsernameGenerator\GeneratesUsernames;

/**
 * @method static create(array $data)
 * @method static findOrFail($id)
 * @method static firstOrNew(array $array)
 * @method static find(int $id)
 * @property int $id
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasAvatar;
    use FindSimilarUsernames;
    use GeneratesUsernames;
    use SoftDeletes;

    private string|null $password;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'avatar',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    public function authProviders(): HasMany
    {
        return $this->hasMany(AuthProvider::class);
    }

    public function isPasswordNull(): bool
    {
        return is_null($this->password);
    }

}
