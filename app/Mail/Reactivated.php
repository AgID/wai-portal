<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

class Reactivated extends UserMailable
{
    public function build(): Reactivated
    {
        return $this->subject(__('Utente riattivato'))
            ->markdown('mail.reactivated')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
            ]);
    }
}
