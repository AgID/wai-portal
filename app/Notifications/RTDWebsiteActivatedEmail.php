<?php

namespace App\Notifications;

use App\Mail\RTDWebsiteActivated;
use App\Models\Website;
use Illuminate\Mail\Mailable;

class RTDWebsiteActivatedEmail extends RTDEmailNotification
{
    protected $website;

    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new RTDWebsiteActivated($notifiable, $this->website);
    }
}
