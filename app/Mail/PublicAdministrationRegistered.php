<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Support\Facades\Lang;

class PublicAdministrationRegistered extends UserMailable
{
    protected $publicAdministration;

    protected $javascriptSnippet;

    public function __construct(User $recipient, PublicAdministration $publicAdministration, string $javascriptSnippet)
    {
        parent::__construct($recipient);
        $this->publicAdministration = $publicAdministration;
        $this->javascriptSnippet = $javascriptSnippet;
    }

    public function build(): PublicAdministrationRegistered
    {
        return $this->subject(__('Pubblica amministrazione registrata'))
            ->markdown('mail.registered_public_administration')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'publicAdministration' => $this->publicAdministration,
                'javascriptSnippet' => $this->javascriptSnippet,
            ]);
    }
}
