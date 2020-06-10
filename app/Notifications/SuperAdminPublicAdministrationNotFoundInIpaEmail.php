<?php

namespace App\Notifications;

use App\Mail\SuperAdminPublicAdministrationNotFoundInIpa;
use Illuminate\Mail\Mailable;

/**
 * Public administration not found in iPA email to super-administrators notification.
 */
class SuperAdminPublicAdministrationNotFoundInIpaEmail extends UserEmailNotification
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
        return new SuperAdminPublicAdministrationNotFoundInIpa($notifiable, $this->publicAdministration);
    }
}
