<?php

namespace App\Notifications;

use App\Mail\PublicAdministrationPurged;
use Illuminate\Mail\Mailable;

class PublicAdministrationPurgedEmail extends UserEmailNotification
{
    protected $publicAdministration;

    public function __construct($publicAdministration)
    {
        $this->publicAdministration = $publicAdministration;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new PublicAdministrationPurged($notifiable, $this->publicAdministration);
    }
}
