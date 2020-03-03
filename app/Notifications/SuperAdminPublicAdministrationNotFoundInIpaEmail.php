<?php

namespace App\Notifications;

use App\Mail\SuperAdminPublicAdministrationNotFoundInIpa;
use App\Models\PublicAdministration;
use Illuminate\Mail\Mailable;

/**
 * Public administration not found in iPA email to super-administrators notification.
 */
class SuperAdminPublicAdministrationNotFoundInIpaEmail extends UserEmailNotification
{
    /**
     * The public administration missing in iPA.
     *
     * @var PublicAdministration the public administration
     */
    protected $publicAdministration;

    /**
     * Default constructor.
     *
     * @param PublicAdministration $publicAdministration the missing public administration
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
        return new SuperAdminPublicAdministrationNotFoundInIpa($notifiable, $this->publicAdministration);
    }
}
