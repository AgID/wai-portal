<?php

namespace App\Notifications;

use App\Mail\UserWebsitePurging;
use App\Models\User;
use App\Models\Website;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

/**
 * Website scheduled for purging user notification.
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

    protected function buildEmail($notifiable): Mailable
    {
        return new UserWebsitePurging($notifiable, $this->website);
    }
}
