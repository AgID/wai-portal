<?php

namespace App\Notifications;

use App\Models\PublicAdministration;
use Illuminate\Mail\Mailable;

/**
 * RTD email notification template.
 */
abstract class RTDEmailNotification extends EmailNotification
{
    /**
     * Build the message.
     *
     * @param PublicAdministration $notifiable the target
     *
     * @return Mailable the mailable
     */
    public function toMail($notifiable): Mailable
    {
        return $this->buildEmail($notifiable)->to($notifiable->rtd_mail, $notifiable->rtd_name ?? null);
    }
}
