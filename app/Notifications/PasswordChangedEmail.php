<?php

namespace App\Notifications;

use App\Mail\PasswordChanged;
use Illuminate\Mail\Mailable;

class PasswordChangedEmail extends UserEmailNotification
{
    protected function buildEmail($notifiable): Mailable
    {
        return new PasswordChanged($notifiable);
    }
}
