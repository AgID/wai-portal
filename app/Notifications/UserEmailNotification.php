<?php

namespace App\Notifications;

use Illuminate\Mail\Mailable;

abstract class UserEmailNotification extends EmailNotification
{
    public function toMail($notifiable): Mailable
    {
        return $this->buildEmail($notifiable)->to($notifiable->email, ($notifiable->full_name !== $notifiable->fiscal_number ? $notifiable->full_name : null));
    }
}
