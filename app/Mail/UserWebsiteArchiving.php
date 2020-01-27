<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

/**
 * User mail for website scheduled for archiving notification.
 */
class UserWebsiteArchiving extends UserMailable
{
    /**
     * The scheduled to be archived website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * The number of days left before archive.
     *
     * @var int the number of days left
     */
    protected $daysLeft;

    /**
     * Mail constructor.
     *
     * @param User $recipient the user
     * @param Website $website the website
     * @param int $daysLeft the number of days left
     */
    public function __construct(User $recipient, Website $website, int $daysLeft)
    {
        parent::__construct($recipient);
        $this->website = $website;
        $this->daysLeft = $daysLeft;
    }

    /**
     * Build the mail.
     *
     * @return UserWebsiteArchiving the mail
     */
    public function build(): UserWebsiteArchiving
    {
        return $this->subject(__('[Attenzione] - Avviso sito web in archiviazione'))
            ->markdown('mail.user_website_archiving')->with([
                'locale' => Lang::getLocale(),
                'user' => $this->recipient,
                'website' => $this->website,
                'daysLeft' => $this->daysLeft,
            ]);
    }
}
