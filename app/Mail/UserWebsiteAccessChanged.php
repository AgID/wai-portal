<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

class UserWebsiteAccessChanged extends UserMailable
{
    protected $modifiedUser;

    public function __construct(User $recipient, User $modifiedUser)
    {
        parent::__construct($recipient);
        $this->modifiedUser = $modifiedUser;
    }

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
