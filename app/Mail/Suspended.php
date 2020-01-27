<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

class Suspended extends UserMailable
{
    public function build(): Suspended
    {
        return $this->subject(__('Utente sospeso'))
            ->markdown('mail.suspended')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
            ]);
    }
}
