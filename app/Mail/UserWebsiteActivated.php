<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

/**
 * Website activated email to public administration administrators.
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
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param Website $website the activated website
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
        return $this->subject(__('Sito web attivato'))
            ->markdown('mail.user_website_activated')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
            ]);
    }
}
