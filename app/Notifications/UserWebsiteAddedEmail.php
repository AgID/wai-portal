<?php

namespace App\Notifications;

use App\Mail\UserWebsiteAdded;
use App\Models\Website;
use Illuminate\Mail\Mailable;

class UserWebsiteAddedEmail extends UserEmailNotification
{
    protected $website;

    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new UserWebsiteAdded($notifiable, $this->website);
    }
}
