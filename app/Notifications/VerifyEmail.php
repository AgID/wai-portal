<?php

namespace App\Notifications;

use App\Enums\UserRole;
use App\Mail\AccountVerification;
use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

/**
 * User email verification.
 */
class VerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The public administration selected for the invitation.
     *
     * @var \App\Models\PublicAdministration the public administration
     */
    public $publicAdministration;

    /**
     * The user issuing the invitation.
     *
     * @var \App\Models\User the inviting user
     */
    public $invitedBy;

    /**
     * Create a new notification instance.
     *
     * @param PublicAdministration|null $publicAdministration the public administration this user belongs to or null if it is a super admin
     * @param User|null $invitedBy the inviting user or null if none
     */
    public function __construct(PublicAdministration $publicAdministration = null, User $invitedBy = null)
    {
        $this->publicAdministration = $publicAdministration;
        $this->invitedBy = $invitedBy;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable the source
     *
     * @return array|string the channels array or the channel name
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable the user
     *
     * @return \App\Mail\AccountVerification the email
     */
    public function toMail($notifiable): AccountVerification
    {
        $accountVerificationMail = new AccountVerification(
            $notifiable,
            $this->verificationUrl($notifiable),
            $this->publicAdministration,
            $this->invitedBy
        );

        return $accountVerificationMail->to($notifiable->email);
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
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'uuid' => $notifiable->getAttribute($notifiable->getRouteKeyName()),
                'hash' => base64_encode(Hash::make($notifiable->email)),
            ]
        );
    }
}
