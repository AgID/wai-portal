<?php

namespace App\Notifications;

use App\Mail\UserWebsiteUrlChanged;
use App\Models\Website;
use Illuminate\Mail\Mailable;

class UserWebsiteUrlChangedEmail extends UserEmailNotification
{
    protected $website;

    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new UserWebsiteUrlChanged($notifiable, $this->website);
    }
}
