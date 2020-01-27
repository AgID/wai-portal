<?php

namespace App\Notifications;

use Illuminate\Mail\Mailable;

abstract class RTDEmailNotification extends EmailNotification
{
    public function toMail($notifiable): Mailable
    {
        return $this->buildEmail($notifiable)->to($notifiable->rtd_mail, $notifiable->rtd_name ?? null);
    }
}
