<?php

namespace App\Notifications;

use App\Mail\PublicAdministrationRegistered;
use App\Models\PublicAdministration;
use Illuminate\Mail\Mailable;

class PublicAdministrationRegisteredEmail extends UserEmailNotification
{
    protected $publicAdministration;

    public function __construct(PublicAdministration $publicAdministration)
    {
        $this->publicAdministration = $publicAdministration;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new PublicAdministrationRegistered($notifiable, $this->publicAdministration, $this->trackingCode());
    }

    protected function trackingCode(): string
    {
        return app()->make('analytics-service')->getJavascriptSnippet(
            $this->publicAdministration->websites()->first()->analytics_id
        );
    }
}
