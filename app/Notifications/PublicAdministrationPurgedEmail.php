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
     * @var stdClass the purged public administration
     */
    protected $purgedPublicAdministration;

    /**
     * Default constructor.
     *
     * @param stdClass $publicAdministration the purged public administration
     */
    public function __construct($purgedPublicAdministration)
    {
        parent::__construct();
        $this->purgedPublicAdministration = $purgedPublicAdministration;
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
        return new PublicAdministrationPurged($notifiable, $this->purgedPublicAdministration);
    }
}
