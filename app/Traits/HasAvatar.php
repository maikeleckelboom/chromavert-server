<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait HasAvatar
{
    protected string|null $avatar;

    public function updateAvatar(UploadedFile $avatar, $storagePath = 'avatars'): void
    {
        tap($this->avatar, function ($previous) use ($avatar, $storagePath) {
            $this->forceFill([
                'avatar' => $avatar->storePublicly(
                    $storagePath, ['disk' => $this->avatarDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->avatarDisk())->delete($previous);
            }
        });
    }

    public function deleteAvatar(): void
    {
        if (is_null($this->avatar)) return;

        Storage::disk($this->avatarDisk())->delete($this->avatar);

        $this->forceFill(['avatar' => null])->save();
    }

    public function profileAvatarUrl(): Attribute
    {
        return Attribute::get(function (): string {
            return $this->avatar
                ? Storage::disk($this->avatarDisk())->url($this->avatar)
                : $this->defaultAvatarUrl();
        });
    }

    protected function defaultAvatarUrl(): string|null
    {
        return null;
    }

    protected function avatarDisk(): string
    {
        return 'public';
    }
}
