<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

/**
 * Website unarchived email to public administration administrators.
 */
class UserWebsiteUnarchived extends UserMailable
{
    /**
     * The unarchived website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param Website $website the unarchived website
     */
    public function __construct(User $recipient, Website $website)
    {
        parent::__construct($recipient);
        $this->website = $website;
    }

    /**
     * Build the message.
     *
     * @return UserWebsiteUnarchived the email
     */
    public function build(): UserWebsiteUnarchived
    {
        return $this->subject(__('Sito web riattivato'))
            ->markdown('mail.user_website_unarchived')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
            ]);
    }
}
