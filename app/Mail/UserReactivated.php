<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

class UserReactivated extends UserMailable
{
    private $reactivatedUser;

    public function __construct(User $recipient, User $reactivatedUser)
    {
        parent::__construct($recipient);
        $this->reactivatedUser = $reactivatedUser;
    }

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
