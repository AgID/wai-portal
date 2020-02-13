<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

class SuperAdminPublicAdministrationNotFoundInIpa extends UserMailable
{
    protected $publicAdministration;

    public function __construct(User $recipient, PublicAdministration $publicAdministration)
    {
        parent::__construct($recipient);
        $this->publicAdministration = $publicAdministration;
    }

    public function build(): SuperAdminPublicAdministrationNotFoundInIpa
    {
        return $this->subject(__('Pubblica amministrazione non trovata in iPA'))
            ->markdown('mail.super_admin_public_administration_not_in_ipa')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
