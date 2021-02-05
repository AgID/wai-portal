<?php

namespace App\Notifications;

use App\Mail\UserWebsiteArchiving;
use App\Models\Website;
use Illuminate\Mail\Mailable;

/**
 * Website scheduled for archiving email notification to public administration administrators.
 */
class UserWebsiteArchivingEmail extends UserEmailNotification
{
    /**
     * The website scheduled for archiving.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Number of days remaining before archiving.
     *
     * @var int the number of days
     */
    protected $daysLeft;

    /**
     * Notification constructor.
     *
     * @param Website $website the website
     * @param int $daysLeft the remaining days
     */
    public function __construct(Website $website, int $daysLeft)
    {
        parent::__construct();
        $this->website = $website;
        $this->daysLeft = $daysLeft;
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
        return new UserWebsiteArchiving($notifiable, $this->website, $this->daysLeft);
    }
}
