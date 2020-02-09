<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

/**
 * Public administration registered email to RTD.
 */
class RTDPublicAdministrationRegistered extends RTDMailable
{
    /**
     * Build the message.
     *
     * @return RTDPublicAdministrationRegistered the email
     */
    public function build(): RTDPublicAdministrationRegistered
    {
        return $this->subject(__('Pubblica amministrazione registrata'))
            ->markdown('mail.rtd_public_administration_registered')->with([
                'locale' => Lang::getLocale(),
                'publicAdministration' => $this->recipient,
            ]);
    }
}
