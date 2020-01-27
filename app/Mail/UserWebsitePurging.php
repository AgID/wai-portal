<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

/**
 * User mail for website scheduled for purging notification.
 */
class UserWebsitePurging extends UserMailable
{
    /**
     * The activated website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Mail constructor.
     *
     * @param User $recipient the user
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
        return $this->subject(__('[Attenzione] - Avviso sito web in eliminazione'))
            ->markdown('mail.user_website_purging')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
            ]);
    }
}
