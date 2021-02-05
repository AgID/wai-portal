<?php

namespace App\Notifications;

use App\Models\PublicAdministration;
use App\Models\User;
use App\Traits\ManageRecipientNotifications;
use Illuminate\Mail\Mailable;

/**
 * User email notification template.
 */
abstract class UserEmailNotification extends EmailNotification
{
    use ManageRecipientNotifications;

    /**
     * The public administration.
     *
     * @var PublicAdministration|null the public administration
     */
    protected $publicAdministration;

    /**
     * The email used by the user for the public administration.
     *
     * @var string the email used by the user for the public administration
     */
    protected $recipientEmail;

    /**
     * Default constructor.
     *
     * @param PublicAdministration $publicAdministration the public administration
     * @param string $recipientEmail the email address to use for thins notification
     */
    public function __construct(PublicAdministration $publicAdministration = null, string $recipientEmail = null)
    {
        $this->publicAdministration = $publicAdministration;
        $this->recipientEmail = $recipientEmail;
    }

    /**
     * Build the message.
     *
     * @param User $notifiable the target
     *
     * @return Mailable the mailable
     */
    public function toMail($notifiable): Mailable
    {
        if (!$this->recipientEmail) {
            $this->recipientEmail = $this->getUserEmailForPublicAdministration($notifiable, $this->publicAdministration);
        }

        return $this->buildEmail($notifiable)->to($this->recipientEmail, ($notifiable->full_name !== $notifiable->email ? $notifiable->full_name : null));
    }
}
