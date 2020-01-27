<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

class PasswordChanged extends UserMailable
{
    public function build(): PasswordChanged
    {
        return $this->subject(__('[Info] - Password modificata'))
            ->markdown('mail.admin_password_changed')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
            ]);
    }
}
