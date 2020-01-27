<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * User suspended email to public administration administrators.
 */
class UserSuspended extends UserMailable
{
    /**
     * The suspended user.
     *
     * @var User the user
     */
    protected $suspendedUser;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param User $suspendedUser the suspended user
     */
    public function __construct(User $recipient, User $suspendedUser)
    {
        parent::__construct($recipient);
        $this->suspendedUser = $suspendedUser;
    }

    /**
     * Build the message.
     *
     * @return UserSuspended the email
     */
    public function build(): UserSuspended
    {
        return $this->subject(__('[Info] - Utente sospeso'))
            ->markdown('mail.user_suspended')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'suspendedUser' => $this->suspendedUser,
            ]);
    }
}
