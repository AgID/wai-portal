<?php

namespace App\Notifications;

use App\Mail\Suspended;
use App\Models\PublicAdministration;
use Illuminate\Mail\Mailable;

/**
 * User suspended email notification.
 */
class SuspendedEmail extends UserEmailNotification
{
    /**
     * The public administration the invited user belogns to.
     *
     * @var PublicAdministration the public administration
     */
    protected $publicAdministration;

    /**
     * Default constructor.
     *
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
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
        return new Suspended($notifiable, $this->publicAdministration);
    }
}
