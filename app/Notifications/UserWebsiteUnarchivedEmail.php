<?php

namespace App\Notifications;

use App\Mail\UserWebsiteUnarchived;
use App\Models\Website;
use Illuminate\Mail\Mailable;

class UserWebsiteUnarchivedEmail extends UserEmailNotification
{
    protected $website;

    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new UserWebsiteUnarchived($notifiable, $this->website);
    }
}
