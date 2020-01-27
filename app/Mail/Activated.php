<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

class Activated extends UserMailable
{
    public function build(): Activated
    {
        return $this->subject(__('Utente attivato'))
            ->markdown('mail.activated')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
            ]);
    }
}
