<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Website;
use Illuminate\Support\Facades\Lang;

/**
 * Website scheduled for archiving email to public administration administrators.
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
     * Default constructor.
     *
     * @param User $recipient the mail recipient
     * @param Website $website the website scheduled for archiving
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
