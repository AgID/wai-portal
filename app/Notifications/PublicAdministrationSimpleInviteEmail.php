<?php

namespace App\Notifications;

use App\Mail\UserSimplePublicAdministrationInvite;
use App\Models\PublicAdministration;
use Illuminate\Mail\Mailable;

/**
 * Public administration activated email notification.
 */
class PublicAdministrationSimpleInviteEmail extends UserEmailNotification
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
    public function buildEmail($notifiable): Mailable
    {
        return new UserSimplePublicAdministrationInvite($notifiable, $this->publicAdministration);
    }
}
