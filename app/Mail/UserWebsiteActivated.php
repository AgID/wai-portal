<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

/**
 * User mail for website activation notification.
 */
class UserWebsiteActivated extends UserMailable
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
     * @return UserWebsiteActivated the mail
     */
    public function build(): UserWebsiteActivated
    {
        return $this->subject(__('[Info] - Sito web attivato'))
            ->markdown('mail.user_website_activated')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
            ]);
    }
}
