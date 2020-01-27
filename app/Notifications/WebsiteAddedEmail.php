<?php

namespace App\Notifications;

use App\Mail\WebsiteAdded;
use App\Models\Website;
use Illuminate\Mail\Mailable;

class WebsiteAddedEmail extends UserEmailNotification
{
    protected $website;

    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new WebsiteAdded($notifiable, $this->website, $this->trackingCode());
    }

    protected function trackingCode(): string
    {
        return app()->make('analytics-service')->getJavascriptSnippet($this->website->analytics_id);
    }
}
