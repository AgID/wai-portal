<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

class UserSuspended extends UserMailable
{
    protected $suspendedUser;

    public function __construct(User $recipient, User $suspendedUser)
    {
        parent::__construct($recipient);
        $this->suspendedUser = $suspendedUser;
    }

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
