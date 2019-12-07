<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Lang;

/**
 * Expired invitation link used email.
 */
class ExpiredInvitationLinkVisited extends Mailable
{
    use Queueable;
    use SerializesModels;

    /**
     * The recipient of the mail.
     *
     * @var User the user
     */
    protected $user;

    /**
     * The invited user the expired link refers to.
     *
     * @var User the invited user
     */
    protected $invitedUser;

    /**
     * Default constructor.
     *
     * @param User $user the user to notify
     * @param User $invitedUser the invited user
     */
    public function __construct(User $user, User $invitedUser)
    {
        $this->user = $user;
        $this->invitedUser = $invitedUser;
    }

    /**
     * Build the mail message.
     *
     * @return ExpiredInvitationLinkVisited the built mailable
     */
    public function build(): ExpiredInvitationLinkVisited
    {
        return $this->subject(__('[Info] - Avviso di utilizzo di un invito scaduto'))
            ->markdown('mail.expired_invitation_link_visited')->with([
                'locale' => Lang::getLocale(),
                'fullName' => $this->user->full_name,
                'invitedFullName' => $this->invitedUser->full_name,
                'profileUrl' => route('users.show', ['user' => $this->invitedUser]),
            ]);
    }
}
