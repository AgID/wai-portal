<?php

namespace App\Notifications;

use App\Mail\Reactivated;
use App\Models\PublicAdministration;
use Illuminate\Mail\Mailable;

/**
 * User reactivated email notification.
 */
class ReactivatedEmail extends UserEmailNotification
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
        return new Reactivated($notifiable, $this->publicAdministration);
    }
}
