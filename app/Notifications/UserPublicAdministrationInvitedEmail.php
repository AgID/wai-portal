<?php

namespace App\Notifications;

use App\Mail\UserPublicAdministrationInvited;
use Illuminate\Mail\Mailable;

/**
 * Public administration activated email notification.
 */
class UserPublicAdministrationInvitedEmail extends UserEmailNotification
{
    /**
     * Initialize the mail message.
     *
     * @param mixed $notifiable the target
     *
     * @return Mailable the mail message
     */
    public function buildEmail($notifiable): Mailable
    {
        return new UserPublicAdministrationInvited($notifiable, $this->publicAdministration);
    }
}
