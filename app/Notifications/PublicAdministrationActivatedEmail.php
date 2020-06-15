<?php

namespace App\Notifications;

use App\Mail\PublicAdministrationActivated;
use Illuminate\Mail\Mailable;

/**
 * Public administration activated email notification.
 */
class PublicAdministrationActivatedEmail extends UserEmailNotification
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
        return new PublicAdministrationActivated($notifiable, $this->publicAdministration);
    }
}
