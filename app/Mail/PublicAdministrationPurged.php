<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

class PublicAdministrationPurged extends UserMailable
{
    protected $publicAdministration;

    public function __construct(User $recipient, $publicAdministration)
    {
        parent::__construct($recipient);
        $this->publicAdministration = $publicAdministration;
    }

    public function build(): PublicAdministrationPurged
    {
        return $this->subject(__('Pubblica amministrazione eliminata'))
            ->markdown('mail.purged_public_administration')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
            ]);
    }
}
