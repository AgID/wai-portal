<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

/**
 * Website URL changed email to public administration administrators.
 */
class UserWebsiteUrlChanged extends UserMailable
{
    /**
     * The website whose URL has changed.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param Website $website the modified website
     */
    public function __construct(User $recipient, Website $website)
    {
        parent::__construct($recipient);
        $this->website = $website;
    }

    /**
     * Build the message.
     *
     * @return UserWebsiteUrlChanged the email
     */
    public function build(): UserWebsiteUrlChanged
    {
        return $this->subject(__('[Info] - URL sito web modificato'))
            ->markdown('mail.user_website_url_changed')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
            ]);
    }
}
