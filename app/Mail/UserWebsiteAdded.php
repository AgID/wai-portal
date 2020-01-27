<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

/**
 * Website added email to public administration administrators.
 */
class UserWebsiteAdded extends UserMailable
{
    /**
     * The added website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param Website $website the added website
     */
    public function __construct(User $recipient, Website $website)
    {
        parent::__construct($recipient);
        $this->website = $website;
    }

    /**
     * Build the message.
     *
     * @return UserWebsiteAdded the email
     */
    public function build(): UserWebsiteAdded
    {
        return $this->subject(__('[Info] - Sito web aggiunto'))
            ->markdown('mail.user_website_added')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
            ]);
    }
}
