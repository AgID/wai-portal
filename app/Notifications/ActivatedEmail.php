<?php

namespace App\Notifications;

use App\Mail\Activated;
use App\Models\PublicAdministration;
use Illuminate\Mail\Mailable;

/**
 * User activated email notification.
 */
class ActivatedEmail extends UserEmailNotification
{
    /**
     * The activated public administration.
     *
     * @var PublicAdministration the public administration
     */
    protected $publicAdministration;

    /**
     * Default constructor.
     *
     * @param PublicAdministration $publicAdministration the activated public administration
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
        return new Activated($notifiable, $this->publicAdministration);
    }
}
