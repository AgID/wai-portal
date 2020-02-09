<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

/**
 * Website scheduled for purging email to public administration administrators.
 */
class UserWebsitePurging extends UserMailable
{
    /**
     * The website scheduled for purging.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param Website $website the website
     */
    public function __construct(User $recipient, Website $website)
    {
        parent::__construct($recipient);
        $this->website = $website;
    }

    /**
     * Build the mail.
     *
     * @return UserWebsitePurging the mail
     */
    public function build(): UserWebsitePurging
    {
        return $this->subject(__('[Attenzione] - Sito web in eliminazione'))
            ->markdown('mail.user_website_purging')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
            ]);
    }
}
