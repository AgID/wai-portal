<?php

namespace App\Notifications;

use App\Mail\RTDPublicAdministrationRegistered;
use Illuminate\Mail\Mailable;

/**
 * Public administration registered email to RTD notification.
 */
class RTDPublicAdministrationRegisteredEmail extends RTDEmailNotification
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
        return new RTDPublicAdministrationRegistered($notifiable);
    }
}
