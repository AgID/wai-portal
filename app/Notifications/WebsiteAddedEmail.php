<?php

namespace App\Notifications;

use App\Mail\WebsiteAdded;
use App\Models\Website;
use Illuminate\Mail\Mailable;

/**
 * Website added email notification.
 */
class WebsiteAddedEmail extends UserEmailNotification
{
    /**
     * The added website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Default constructor.
     *
     * @param Website $website the added website
     */
    public function __construct(Website $website)
    {
        parent::__construct();
        $this->website = $website;
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
        return new WebsiteAdded($notifiable, $this->website, $this->trackingCode());
    }

    protected function trackingCode(): string
    {
        return app()->make('analytics-service')->getJavascriptSnippet($this->website->analytics_id);
    }
}
