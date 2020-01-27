<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

class RTDPublicAdministrationRegistered extends RTDMailable
{
    public function build(): RTDPublicAdministrationRegistered
    {
        return $this->subject(__('[Info] - Pubblica amministrazione registrata'))
            ->markdown('mail.rtd_public_administration_registered')->with([
                'locale' => Lang::getLocale(),
                'publicAdministration' => $this->recipient,
            ]);
    }
}
