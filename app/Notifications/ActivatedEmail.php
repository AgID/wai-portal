<?php

namespace App\Notifications;

use App\Mail\Activated;
use Illuminate\Mail\Mailable;

/**
 * User activated email notification.
 */
class ActivatedEmail extends UserEmailNotification
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
        return new Activated($notifiable, $this->publicAdministration);
    }
}
