<?php

namespace App\Notifications;

use App\Mail\Suspended;
use Illuminate\Mail\Mailable;

/**
 * User suspended email notification.
 */
class SuspendedEmail extends UserEmailNotification
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
        return new Suspended($notifiable);
    }
}
