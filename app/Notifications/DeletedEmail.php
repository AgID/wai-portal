<?php

namespace App\Notifications;

use App\Mail\Deleted;
use Illuminate\Mail\Mailable;

/**
 * User suspended email notification.
 */
class DeletedEmail extends UserEmailNotification
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
        return new Deleted($notifiable, $this->publicAdministration);
    }
}
