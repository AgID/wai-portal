<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

/**
 * Password changed email.
 */
class PasswordChanged extends UserMailable
{
    /**
     * Build the message.
     *
     * @return PasswordChanged the email
     */
    public function build(): PasswordChanged
    {
        return $this->subject(__('[Info] - Password modificata'))
            ->markdown('mail.admin_password_changed')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
            ]);
    }
}
