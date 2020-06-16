<?php

namespace App\Mail;

use Illuminate\Support\Facades\Lang;

/**
 * Public administration not found in iPA email to super-administrators.
 */
class SuperAdminPublicAdministrationNotFoundInIpa extends UserMailable
{
    /**
     * Build the message.
     *
     * @return SuperAdminPublicAdministrationNotFoundInIpa the email
     */
    public function build(): SuperAdminPublicAdministrationNotFoundInIpa
    {
        return $this->subject(__('Pubblica amministrazione non trovata in IPA'))
            ->markdown('mail.super_admin_public_administration_not_in_ipa')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
