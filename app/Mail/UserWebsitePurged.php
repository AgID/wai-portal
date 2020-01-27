<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Support\Facades\Lang;

/**
 * Website purged email to public administration administrators.
 */
class UserWebsitePurged extends UserMailable
{
    /**
     * The purged website.
     *
     * @var mixed the website
     */
    protected $website;

    /**
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param mixed $website the purged website
     */
    public function __construct(User $recipient, $website)
    {
        parent::__construct($recipient);
        $this->website = $website;
    }

    /**
     * Build the message.
     *
     * @return UserWebsitePurged the email
     */
    public function build(): UserWebsitePurged
    {
        return $this->subject(__('[Attenzione] - Sito web eliminato'))
            ->markdown('mail.user_website_purged')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
            ]);
    }
}
