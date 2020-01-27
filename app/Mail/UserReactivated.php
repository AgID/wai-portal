<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * User reactivated email to public administration administrators.
 */
class UserReactivated extends UserMailable
{
    /**
     * The reactivated user.
     *
     * @var User the user
     */
    private $reactivatedUser;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param User $reactivatedUser the reactivated user
     */
    public function __construct(User $recipient, User $reactivatedUser)
    {
        parent::__construct($recipient);
        $this->reactivatedUser = $reactivatedUser;
    }

    /**
     * Build the message.
     *
     * @return UserReactivated the email
     */
    public function build(): UserReactivated
    {
        return $this->subject(__('[Info] - Utente riattivato'))
            ->markdown('mail.user_reactivated')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'reactivatedUser' => $this->reactivatedUser,
            ]);
    }
}
