<?php

namespace App\Notifications;

use App\Enums\UserRole;
use App\Mail\AccountVerification;
use App\Models\PublicAdministration;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

/**
 * User email verification.
 */
class VerifyEmail extends UserEmailNotification
{
    /**
     * The public administration selected for the invitation.
     *
     * @var \App\Models\PublicAdministration the public administration
     */
    protected $publicAdministration;

    /**
     * Create a new notification instance.
     *
     * @param PublicAdministration|null $publicAdministration the public administration this user belongs to or null if it is a super admin
     */
    public function __construct(?PublicAdministration $publicAdministration = null)
    {
        $this->publicAdministration = $publicAdministration;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable the user
     *
     * @return \App\Mail\AccountVerification the email
     */
    protected function buildEmail($notifiable): Mailable
    {
        return new AccountVerification(
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
