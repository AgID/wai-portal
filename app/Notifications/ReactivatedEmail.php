<?php

namespace App\Notifications;

use App\Mail\Reactivated;
use Illuminate\Mail\Mailable;

class ReactivatedEmail extends UserEmailNotification
{
    protected function buildEmail($notifiable): Mailable
    {
        return new Reactivated($notifiable);
    }
}
