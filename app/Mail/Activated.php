<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * User activated email.
 */
class Activated extends UserMailable
{
    /**
     * Build the message.
     *
     * @return Activated the email
     */
    public function build(): Activated
    {
        return $this->subject(__('Utente attivato'))
            ->markdown('mail.activated')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
            ]);
    }
}
