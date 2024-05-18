<?php

namespace App;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;


trait HasProfilePhoto
{
    /**
     * Update the user's profile photo.
     *
     * @param UploadedFile $photo
     * @param string $storagePath
     * @return void
     */
    public function updateProfilePhoto(
        UploadedFile $photo,
        string       $storagePath = 'profile-photos',
    ): void
    {
//        if ($photo->getClientOriginalExtension() !== 'avif') {
//           $this->resizeImage($photo, 192);
//        }

        tap($this->profile_photo_path, function ($previous) use ($photo, $storagePath) {
            $this->forceFill([
                'profile_photo_path' => $photo->storePublicly(
                    $storagePath, ['disk' => $this->profilePhotoDisk()]
                ),
            ])->save();

            if ($previous) {
                Storage::disk($this->profilePhotoDisk())->delete($previous);
            }
        });
    }

    /**
     * Delete the user's profile photo.
     *
     * @return void
     */
    public function deleteProfilePhoto(): void
    {
        if (is_null($this->profile_photo_path)) {
            return;
        }

        Storage::disk($this->profilePhotoDisk())
            ->delete($this->profile_photo_path);

        $this->forceFill([
            'profile_photo_path' => null,
        ])->save();
    }

    /**
     * Get the URL to the user's profile photo.
     *
     * @return Attribute
     */
    public function profilePhotoUrl(): Attribute
    {
        return Attribute::get(function (): string|null {
            if ($this->profilePhotoIsUrl()) {
                return $this->profile_photo_path;
            }

            $resolvedUrl = $this->profile_photo_path
                ? Storage::disk($this->profilePhotoDisk())->url($this->profile_photo_path)
                : $this->defaultProfilePhotoUrl();
//
//            if (config('app.env') === 'local') {
//                return $this->removeStoragePathPrefix($resolvedUrl);
//            }

            return $this->removeStoragePathPrefix($resolvedUrl);
        });
    }

    protected function resizeImage(UploadedFile $photo, int $size)
    {
        $manager = new ImageManager(new Driver());
        $img = $manager->read($photo->getRealPath());
        $img->resize($size, $size);
        return $img->save($photo->getRealPath());
    }

    protected function removeStoragePathPrefix($path): string
    {
        return str_replace('storage/', '', $path);
    }


    protected function profilePhotoIsUrl(): bool
    {
        return $this->profile_photo_path !== null && filter_var($this->profile_photo_path, FILTER_VALIDATE_URL);
    }

    /**
     * Get the disk that profile photos should be stored on.
     *
     * @return string
     */
    protected function profilePhotoDisk()
    {
        return 'public';
    }

    protected function defaultProfilePhotoUrl()
    {
        return null;
    }
}
