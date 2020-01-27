<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

class UserActivated extends UserMailable
{
    protected $activatedUser;

    public function __construct(User $recipient, User $activatedUser)
    {
        parent::__construct($recipient);
        $this->activatedUser = $activatedUser;
    }

    public function build(): UserActivated
    {
        return $this->subject(__('[Info] - Utente attivato'))
            ->markdown('mail.user_activated')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'activatedUser' => $this->activatedUser,
            ]);
    }
}
