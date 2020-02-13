<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

class RTDEmailAddressChanged extends RTDMailable
{
    protected $earliestRegisteredAdministrator;

    public function __construct(PublicAdministration $recipient, User $earliestRegisteredAdministrator)
    {
        parent::__construct($recipient);
        $this->earliestRegisteredAdministrator = $earliestRegisteredAdministrator;
    }

    public function build(): RTDEmailAddressChanged
    {
        return $this->subject(__('Nuovo indirizzo email RTD'))
            ->markdown('mail.rtd_email_changed')->with([
                'locale' => Lang::getLocale(),
                'publicAdministration' => $this->recipient,
                'earliestRegisteredAdministrator' => $this->earliestRegisteredAdministrator,
            ]);
    }
}
