<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

/**
 * Primary website not tracking email to public administration administrators.
 */
class UserPrimaryWebsiteNotTracking extends UserMailable
{
    /**
     * Build the message.
     *
     * @return UserPrimaryWebsiteNotTracking the email
     */
    public function build(): UserPrimaryWebsiteNotTracking
    {
        return $this->subject(__('[Attenzione] - Tracciamento sito istituzionale non attivo'))
            ->markdown('mail.user_primary_website_not_tracking')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
            ]);
    }
}
