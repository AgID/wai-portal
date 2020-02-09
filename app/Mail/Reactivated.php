<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

/**
 * User reactivated email.
 */
class Reactivated extends UserMailable
{
    /**
     * Build the message.
     *
     * @return Reactivated the email
     */
    public function build(): Reactivated
    {
        return $this->subject(__('Utente riattivato'))
            ->markdown('mail.reactivated')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
            ]);
    }
}
