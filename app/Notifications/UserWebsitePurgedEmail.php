<?php

namespace App\Notifications;

use App\Mail\UserWebsitePurged;
use Illuminate\Mail\Mailable;

class UserWebsitePurgedEmail extends UserEmailNotification
{
    protected $website;

    public function __construct($website)
    {
        $this->website = $website;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new UserWebsitePurged($notifiable, $this->website);
    }
}
