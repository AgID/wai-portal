<?php

namespace App\Notifications;

use App\Mail\AccountVerification;
use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The public administration selected for the invitation.
     *
     * @var App\Models\PublicAdministration
     */
    public $publicAdministration;

    /**
     * The user issuing the invitation.
     *
     * @var App\Models\User
     */
    public $invitedBy;

    /**
     * Create a new notification instance.
     *
     * @param PublicAdministration|null $publicAdministration
     * @param User|null $invitedBy
     *
     * @return void
     */
    public function __construct(PublicAdministration $publicAdministration = null, User $invitedBy = null)
    {
        $this->publicAdministration = $publicAdministration;
        $this->invitedBy = $invitedBy;
    }

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     *
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return App\Mail\AccountVerification
     */
    public function toMail($notifiable)
    {
        $accountVerificationMail = new AccountVerification(
            $notifiable,
            $this->publicAdministration,
            $this->invitedBy,
            $this->verificationUrl($notifiable)
        );

        return ($accountVerificationMail)->to($notifiable->email);
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     *
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        $verificationRoute = $notifiable->isA('super-admin')
            ? 'admin.verification.verify'
            : 'verification.verify';

        return URL::temporarySignedRoute(
            $verificationRoute,
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            ['id' => $notifiable->getKey()]
        );
    }
}
