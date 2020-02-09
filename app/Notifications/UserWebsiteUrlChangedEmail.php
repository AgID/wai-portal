<?php

namespace App\Notifications;

use App\Mail\UserWebsiteUrlChanged;
use App\Models\Website;
use Illuminate\Mail\Mailable;

/**
 * Website URL changed email notification to public administration administrators.
 */
class UserWebsiteUrlChangedEmail extends UserEmailNotification
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
     * @param Website $website the modified website
     */
    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    /**
     * Initialize the mail message.
     *
     * @param mixed $notifiable the target
     *
     * @return Mailable the mail message
     */
    protected function buildEmail($notifiable): Mailable
    {
        return new UserWebsiteUrlChanged($notifiable, $this->website);
    }
}
