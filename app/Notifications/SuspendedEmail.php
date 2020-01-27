<?php

namespace App\Notifications;

use App\Mail\Suspended;
use Illuminate\Mail\Mailable;

class SuspendedEmail extends UserEmailNotification
{
    protected function buildEmail($notifiable): Mailable
    {
        return new Suspended($notifiable);
    }
}
