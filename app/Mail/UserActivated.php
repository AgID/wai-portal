<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * User activated email to public administration administrators.
 */
class UserActivated extends UserMailable
{
    /**
     * The activated user.
     *
     * @var User the user
     */
    protected $activatedUser;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param User $activatedUser the activated user
     */
    public function __construct(User $recipient, User $activatedUser)
    {
        parent::__construct($recipient);
        $this->activatedUser = $activatedUser;
    }

    /**
     * Build the message.
     *
     * @return UserActivated the email
     */
    public function build(): UserActivated
    {
        return $this->subject(__('Utente attivato'))
            ->markdown('mail.user_activated')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'activatedUser' => $this->activatedUser,
            ]);
    }
}
