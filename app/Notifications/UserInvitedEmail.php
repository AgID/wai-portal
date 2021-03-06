<?php

namespace App\Notifications;

use App\Mail\UserInvited;
use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Mail\Mailable;

/**
 * User invited email notification to public administration administrators.
 */
class UserInvitedEmail extends UserEmailNotification
{
    /**
     * The invited user.
     *
     * @var User the user
     */
    protected $invitedUser;

    /**
     * Default constructor.
     *
     * @param User $invitedUser the invited user
     * @param PublicAdministration $publicAdministration the public administration the user belongs to
     */
    public function __construct(User $invitedUser, PublicAdministration $publicAdministration)
    {
        parent::__construct($publicAdministration);
        $this->invitedUser = $invitedUser;
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
        return new UserInvited($notifiable, $this->invitedUser, $this->publicAdministration);
    }
}
