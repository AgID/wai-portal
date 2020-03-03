<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * User invited email to public administration administrators.
 */
class UserInvited extends UserMailable
{
    /**
     * The invited user.
     *
     * @var \App\Models\PublicAdministration
     */
    private $invitedUser;

    /**
     * The public administration the invited user belongs to.
     *
     * @var \App\Models\PublicAdministration
     */
    private $publicAdministration;

    /**
     * Create a new mail message instance.
     *
     * @param User $recipient the mail recipient
     * @param User $invitedUser the invited user
     * @param PublicAdministration $publicAdministration the public administration the invited user belogns to
     */
    public function __construct(User $recipient, User $invitedUser, PublicAdministration $publicAdministration)
    {
        parent::__construct($recipient);
        $this->invitedUser = $invitedUser;
        $this->publicAdministration = $publicAdministration;
    }

    /**
     * Build the message.
     *
     * @return \App\Mail\UserInvited the email
     */
    public function build(): UserInvited
    {
        return $this->subject(__('Nuovo utente invitato'))
            ->markdown('mail.user_invited')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'invitedUser' => $this->invitedUser,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
