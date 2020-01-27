<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

class UserPrimaryWebsiteNotTracking extends UserMailable
{
    public function build(): UserPrimaryWebsiteNotTracking
    {
        return $this->subject(__('[Attenzione] - Tracciamento sito istituzionale non attivo'))
            ->markdown('mail.user_primary_website_not_tracking')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
            ]);
    }
}
