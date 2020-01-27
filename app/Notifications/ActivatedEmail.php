<?php

namespace App\Notifications;

use App\Mail\Activated;
use Illuminate\Mail\Mailable;

class ActivatedEmail extends UserEmailNotification
{
    protected function buildEmail($notifiable): Mailable
    {
        return new Activated($notifiable);
    }
}
