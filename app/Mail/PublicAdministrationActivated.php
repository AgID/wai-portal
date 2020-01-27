<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

class PublicAdministrationActivated extends UserMailable
{
    protected $publicAdministration;

    public function __construct(User $recipient, PublicAdministration $publicAdministration)
    {
        parent::__construct($recipient);
        $this->publicAdministration = $publicAdministration;
    }

    public function build(): PublicAdministrationActivated
    {
        return $this->subject(__('[Info] - Pubblica amministrazione attiva'))
            ->markdown('mail.activated_public_administration')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
