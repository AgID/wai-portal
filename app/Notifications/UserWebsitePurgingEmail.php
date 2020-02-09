<?php

namespace App\Notifications;

use App\Mail\UserWebsitePurging;
use App\Models\Website;
use Illuminate\Mail\Mailable;

/**
 * Website scheduled for purging email notification to public administration administrators.
 */
class UserWebsitePurgingEmail extends UserEmailNotification
{
    /**
     * The activated website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Notification constructor.
     *
     * @param Website $website the website
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
        return new UserWebsitePurging($notifiable, $this->website);
    }
}
