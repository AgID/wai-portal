<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * Expired invitation link used email to public administration administrators.
 */
class UserExpiredInvitationLinkVisited extends UserMailable
{
    /**
     * The invited user the expired link refers to.
     *
     * @var User the user
     */
    protected $invitedUser;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param User $invitedUser the invited user
     */
    public function __construct(User $recipient, User $invitedUser)
    {
        parent::__construct($recipient);
        $this->invitedUser = $invitedUser;
    }

    /**
     * Build the mail message.
     *
     * @return UserExpiredInvitationLinkVisited the built mailable
     */
    public function build(): UserExpiredInvitationLinkVisited
    {
        return $this->subject(__('[Info] - Avviso di utilizzo di un invito scaduto'))
            ->markdown('mail.user_expired_invitation_link_visited')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'invitedUser' => $this->invitedUser,
            ]);
    }
}
