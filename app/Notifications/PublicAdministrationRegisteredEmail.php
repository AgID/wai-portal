<?php

namespace App\Notifications;

use App\Mail\PublicAdministrationRegistered;
use App\Models\PublicAdministration;
use Illuminate\Mail\Mailable;

/**
 * Public administration registered email notification.
 */
class PublicAdministrationRegisteredEmail extends UserEmailNotification
{
    /**
     * The registered public administration.
     *
     * @var PublicAdministration the public administration
     */
    protected $publicAdministration;

    /**
     * Default constructor.
     *
     * @param PublicAdministration $publicAdministration the registered public administration
     */
    public function __construct(PublicAdministration $publicAdministration)
    {
        $this->publicAdministration = $publicAdministration;
    }

    /**
     * Initialize the mail message.
     *
     * @param mixed $notifiable the target
     *
     * @return Mailable the mail message
     */
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
