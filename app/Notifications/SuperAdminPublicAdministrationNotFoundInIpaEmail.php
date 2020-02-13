<?php

namespace App\Notifications;

use App\Mail\SuperAdminPublicAdministrationNotFoundInIpa;
use App\Models\PublicAdministration;
use Illuminate\Mail\Mailable;

class SuperAdminPublicAdministrationNotFoundInIpaEmail extends UserEmailNotification
{
    protected $publicAdministration;

    public function __construct(PublicAdministration $publicAdministration)
    {
        $this->publicAdministration = $publicAdministration;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new SuperAdminPublicAdministrationNotFoundInIpa($notifiable, $this->publicAdministration);
    }
}
