<?php

namespace App\Notifications;

use App\Mail\UserEmailForPublicAdministrationChanged;
use Illuminate\Mail\Mailable;

/**
 * User reactivated email notification.
 */
class UserPublicAdministrationChangedEmail extends UserEmailNotification
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
        return new UserEmailForPublicAdministrationChanged($notifiable, $this->publicAdministration);
    }
}
