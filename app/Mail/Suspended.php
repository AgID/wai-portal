<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

/**
 * User suspended email.
 */
class Suspended extends UserMailable
{
    /**
     * Build the message.
     *
     * @return Suspended the email
     */
    public function build(): Suspended
    {
        return $this->subject(__('Utente sospeso'))
            ->markdown('mail.suspended')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
            ]);
    }
}
