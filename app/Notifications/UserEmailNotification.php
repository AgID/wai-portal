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
     * Default constructor.
     *
     * @param PublicAdministration $publicAdministration the public administration
     */
    public function __construct(?PublicAdministration $publicAdministration = null)
    {
        $this->publicAdministration = $publicAdministration;
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
        $recipientEmail = $this->getUserEmailForPublicAdministration($notifiable, $this->publicAdministration);

        return $this->buildEmail($notifiable)->to($recipientEmail, ($notifiable->full_name !== $notifiable->email ? $notifiable->full_name : null));
    }
}
