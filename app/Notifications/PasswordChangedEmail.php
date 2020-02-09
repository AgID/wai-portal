<?php

namespace App\Notifications;

use App\Mail\PasswordChanged;
use Illuminate\Mail\Mailable;

/**
 * Password changed email notification.
 */
class PasswordChangedEmail extends UserEmailNotification
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
        return new PasswordChanged($notifiable);
    }
}
