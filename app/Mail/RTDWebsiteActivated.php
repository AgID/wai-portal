<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

class RTDWebsiteActivated extends RTDMailable
{
    protected $website;

    public function __construct(PublicAdministration $recipient, Website $website)
    {
        parent::__construct($recipient);
        $this->website = $website;
    }

    public function build(): RTDWebsiteActivated
    {
        return $this->subject(__('[Info] - Sito web attivato'))
            ->markdown('mail.rtd_website_activated')->with([
                'locale' => Lang::getLocale(),
                'publicAdministration' => $this->recipient,
                'website' => $this->website,
            ]);
    }
}
