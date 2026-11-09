<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class GoogleAuthService
{
    /**
     * Verify domain restrictions if GOOGLE_ALLOWED_DOMAIN is configured.
     *
     * @throws ValidationException
     */
    public function validateAllowedDomain(string $email): void
    {
        $allowedDomain = config('services.google.allowed_domain');

        if (!empty($allowedDomain)) {
            $userDomain = substr(strrchr($email, '@'), 1);

            if (strcasecmp($userDomain, $allowedDomain) !== 0) {
                throw ValidationException::withMessages([
                    'email' => ["Akses hanya diperbolehkan untuk akun dengan domain @{$allowedDomain}."],
                ]);
            }
        }
    }

    /**
     * Find or create local user from Google Socialite payload.
     *
     * @throws ValidationException
     */
    public function findOrCreateUser(SocialiteUser $googleUser): User
    {
        $email = $googleUser->getEmail();

        if (empty($email)) {
            throw ValidationException::withMessages([
                'email' => ['Alamat email Google tidak ditemukan atau tidak dapat diakses.'],
            ]);
        }

        $this->validateAllowedDomain($email);

        return User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Google User',
                'google_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
            ]
        );
    }
}

