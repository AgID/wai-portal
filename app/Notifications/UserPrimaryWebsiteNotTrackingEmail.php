<?php

namespace App\Notifications;

use App\Mail\UserPrimaryWebsiteNotTracking;
use Illuminate\Mail\Mailable;

/**
 * Primary website not tracking user notification.
 */
class UserPrimaryWebsiteNotTrackingEmail extends UserEmailNotification
{
    /**
     * Initialize the mail message.
     *
     * @param mixed $notifiable the target
     *
     * @return Mailable the mail message
     */
    protected function buildEmail($notifiable): Mailable
    {
        return new UserPrimaryWebsiteNotTracking($notifiable);
    }
}
