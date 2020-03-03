<?php

namespace App\Notifications;

use App\Mail\UserExpiredInvitationLinkVisited;
use App\Models\User;
use Illuminate\Mail\Mailable;

/**
 * Expired URL visited notification to public administration administrators.
 */
class UserExpiredInvitationLinkVisitedEmail extends UserEmailNotification
{
    /**
     * The user the expired URL refers to.
     *
     * @var User the user
     */
    protected $invitedUser;

    /**
     * Default constructor.
     *
     * @param User $invitedUser the user
     */
    public function __construct(User $invitedUser)
    {
        $this->invitedUser = $invitedUser;
    }

    /**
     * Build the message.
     *
     * @param User $notifiable the user
     *
     * @return UserExpiredInvitationLinkVisited the mail message
     */
    protected function buildEmail($notifiable): Mailable
    {
        return new UserExpiredInvitationLinkVisited($notifiable, $this->invitedUser);
    }
}
