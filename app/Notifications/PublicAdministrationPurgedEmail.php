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
     * string email for the user in the public administration.
     *
     * @var string the JSON string
     */
    protected $userEmailForPublicAdministration;

    /**
     * Default constructor.
     *
     * @param mixed $publicAdministration the purged public administration
     */
    public function __construct($publicAdministration, $email)
    {
        $this->publicAdministration = $publicAdministration;
        $this->userEmailForPublicAdministration = $email;
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
        return new PublicAdministrationPurged($notifiable, $this->publicAdministration, $this->userEmailForPublicAdministration);
    }
}
