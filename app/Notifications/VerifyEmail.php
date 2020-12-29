<?php

namespace App\Notifications;

use App\Enums\UserRole;
use App\Mail\EmailVerification;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

/**
 * User verification email notification.
 */
class VerifyEmail extends UserEmailNotification
{
    /**
     * Initialize the mail message.
     *
     * @param mixed $notifiable the target
     *
     * @return Mailable the mail message
     */
    protected function buildEmail($notifiable): Mailable
    {
        return new EmailVerification(
            $notifiable,
            $this->verificationUrl($notifiable),
            $this->publicAdministration,
        );
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable the notification source
     *
     * @return string the URL
     */
    protected function verificationUrl($notifiable): string
    {
        $verificationRoute = $notifiable->isA(UserRole::SUPER_ADMIN)
            ? 'admin.verification.verify'
            : 'verification.verify';

        return URL::temporarySignedRoute(
            $verificationRoute,
            Carbon::now()->addDays(config('auth.verification.expire', 7)),
            [
                'uuid' => $notifiable->getAttribute($notifiable->getRouteKeyName()),
                'hash' => base64_encode(Hash::make($notifiable->email)),
            ]
        );
    }
}
