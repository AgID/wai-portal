<?php

namespace App\Notifications;

use App\Mail\PublicAdministrationPurged;
use Illuminate\Mail\Mailable;

/**
 * Public administration purged email notification.
 */
class PublicAdministrationPurgedEmail extends UserEmailNotification
{
    /**
     * The purged public administration.
     *
     * @var mixed the public administration
     */
    protected $publicAdministration;

    /**
     * Default constructor.
     *
     * @param mixed $publicAdministration the purged public administration
     */
    public function __construct($publicAdministration)
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
        return new PublicAdministrationPurged($notifiable, $this->publicAdministration);
    }
}
