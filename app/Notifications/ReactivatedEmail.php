<?php

namespace App\Notifications;

use App\Mail\Reactivated;
use Illuminate\Mail\Mailable;

/**
 * User reactivated email notification.
 */
class ReactivatedEmail extends UserEmailNotification
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
        return new Reactivated($notifiable);
    }
}
