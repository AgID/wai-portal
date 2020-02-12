<?php

namespace App\Notifications;

use App\Mail\RTDPublicAdministrationRegistered;
use App\Models\User;
use Illuminate\Mail\Mailable;

/**
 * Public administration registered email to RTD notification.
 */
class RTDPublicAdministrationRegisteredEmail extends RTDEmailNotification
{
    /**
     * The registering user.
     *
     * @var User the user
     */
    protected $registeringUser;

    /**
     * Default constructor.
     *
     * @param User $registeringUser the registering user
     */
    public function __construct(User $registeringUser)
    {
        $this->registeringUser = $registeringUser;
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
        return new RTDPublicAdministrationRegistered($notifiable, $this->registeringUser);
    }
}
