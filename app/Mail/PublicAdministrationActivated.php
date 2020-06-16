<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

/**
 * Public administration activated email.
 */
class PublicAdministrationActivated extends UserMailable
{
    /**
     * Build the message.
     *
     * @return PublicAdministrationActivated the email
     */
    public function build(): PublicAdministrationActivated
    {
        return $this->subject(__('Pubblica amministrazione attivata'))
            ->markdown('mail.activated_public_administration')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
