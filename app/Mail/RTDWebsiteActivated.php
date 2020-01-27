<?php

namespace App\Mail;

use App\Models\PublicAdministration;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

/**
 * Website activated email to RTD.
 */
class RTDWebsiteActivated extends RTDMailable
{
    /**
     * The activated website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Default constructor.
     *
     * @param PublicAdministration $recipient the public administration the website belongs to
     * @param Website $website the website
     */
    public function __construct(PublicAdministration $recipient, Website $website)
    {
        parent::__construct($recipient);
        $this->website = $website;
    }

    /**
     * Build the message.
     *
     * @return RTDWebsiteActivated the email
     */
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
