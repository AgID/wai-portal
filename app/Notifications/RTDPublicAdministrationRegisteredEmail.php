<?php

namespace App\Notifications;

use App\Mail\RTDPublicAdministrationRegistered;
use Illuminate\Mail\Mailable;

class RTDPublicAdministrationRegisteredEmail extends RTDEmailNotification
{
    protected function buildEmail($notifiable): Mailable
    {
        return new RTDPublicAdministrationRegistered($notifiable);
    }
}
