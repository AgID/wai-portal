<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * User permissions changed email to public administration administrators.
 */
class UserWebsiteAccessChanged extends UserMailable
{
    /**
     * The modified user.
     *
     * @var User the user
     */
    protected $modifiedUser;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param User $modifiedUser the modified user
     */
    public function __construct(User $recipient, User $modifiedUser)
    {
        parent::__construct($recipient);
        $this->modifiedUser = $modifiedUser;
    }

    /**
     * Build the message.
     *
     * @return UserWebsiteAccessChanged the email
     */
    public function build(): UserWebsiteAccessChanged
    {
        return $this->subject(__('[Info] - Permessi utente modificati'))
            ->markdown('mail.user_website_access_changed')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'modifiedUser' => $this->modifiedUser,
            ]);
    }
}
