<?php

namespace App\Notifications;

use App\Mail\UserWebsiteActivated;
use App\Models\Website;
use Illuminate\Mail\Mailable;

class UserWebsiteActivatedEmail extends UserEmailNotification
{
    protected $website;

    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new UserWebsiteActivated($notifiable, $this->website);
    }
}
