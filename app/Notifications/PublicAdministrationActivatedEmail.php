<?php

namespace App\Notifications;

use App\Mail\PublicAdministrationActivated;
use App\Models\PublicAdministration;
use Illuminate\Mail\Mailable;

class PublicAdministrationActivatedEmail extends UserEmailNotification
{
    protected $publicAdministration;

    public function __construct(PublicAdministration $publicAdministration)
    {
        $this->publicAdministration = $publicAdministration;
    }

    public function buildEmail($notifiable): Mailable
    {
        return new PublicAdministrationActivated($notifiable, $this->publicAdministration);
    }
}
