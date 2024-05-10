<?php

namespace App\Traits;

use App\Http\Services\UserService;

trait HasProviderData
{
    public function getAvatarsList()
    {
        $currentUser = (new UserService())->getCurrentUser();
        $currentUserAvatar = $currentUser->avatar;

        $providedAvatars = $this->authProviders()
            ->pluck('provider_user_avatar')
            ->reject(fn($avatar) => $avatar === $currentUserAvatar || is_null($avatar))
            ->map(fn($avatar) => [
                'value' => $avatar,
                'selected' => $avatar === $currentUserAvatar,
            ]);

        if (!is_null($currentUserAvatar)) {
            $providedAvatars = $providedAvatars->prepend([
                'value' => $currentUserAvatar,
                'selected' => true,
            ]);
        }

        return $providedAvatars;
    }

    public function getUsernames()
    {
        $currentUser = (new UserService())->getCurrentUser();
        $currentUserUsername = $currentUser->username;

        $nicknames = $this->authProviders()
            ->pluck('provider_user_nickname')
            ->reject(fn($nickname) => $nickname === $currentUserUsername || is_null($nickname))
            ->map(fn($username) => [
                'value' => $username,
                'selected' => $username === $currentUserUsername,
            ])
            ->values();

        if (!is_null($currentUserUsername)) {
            $nicknames = $nicknames->prepend([
                'value' => $currentUserUsername,
                'selected' => true,
            ]);
        }

        return $nicknames;
    }

    public function getEmailAddresses()
    {
        $currentUser = (new UserService())->getCurrentUser();
        $currentUserEmail = $currentUser->email;
        $isSameAsEmail = fn($email) => $email === $currentUser->email;;

        return $this->authProviders()
            ->pluck('provider_user_email')
            ->reject(fn($email) => $isSameAsEmail($email) || is_null($email))
            ->map(fn($email) => [
                'value' => $email,
                'selected' => $isSameAsEmail($email),
                'verified' => $isSameAsEmail($email)
                    ? $this->isEmailVerified()
                    : $this->isEmailExistent($email),
            ])
            ->prepend([
                'value' => $currentUserEmail,
                'verified' => $this->isEmailVerified(),
                'selected' => true,
            ]);
    }

    public function getNames()
    {
        $currentUser = (new UserService())->getCurrentUser();
        $currentUserName = $currentUser->name;

        return $this->authProviders()->pluck('provider_user_name')
            ->reject(fn($name) => $name === $currentUserName)
            ->map(fn($name) => [
                'value' => $name,
                'selected' => $name === $currentUserName,
            ])
            ->prepend([
                'value' => $currentUserName,
                'selected' => true,
            ]);
    }

    private function isEmailExistent(string $email): bool
    {
        return $this->authProviders()->where('provider_user_email', $email)->exists();
    }
}
