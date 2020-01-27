<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

/**
 * User mail for website archived notification.
 */
class UserWebsiteArchived extends UserMailable
{
    /**
     * The archived website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Manual flag.
     *
     * @var bool wether the website was archived manually
     */
    protected $manually;

    /**
     * Mail constructor.
     *
     * @param User $recipient the user
     * @param Website $website the website
     * @param bool $manually whether the website was archived manually
     */
    public function __construct(User $recipient, Website $website, bool $manually)
    {
        parent::__construct($recipient);
        $this->website = $website;
        $this->manually = $manually;
    }

    /**
     * Build the mail.
     *
     * @return UserWebsiteArchived the mail
     */
    public function build(): UserWebsiteArchived
    {
        return $this->subject(__('[Info] - Sito web archiviato'))
            ->markdown('mail.user_website_archived')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
                'manually' => $this->manually,
            ]);
    }
}
