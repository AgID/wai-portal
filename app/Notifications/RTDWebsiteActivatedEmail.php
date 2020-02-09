<?php

namespace App\Notifications;

use App\Mail\RTDWebsiteActivated;
use App\Models\Website;
use Illuminate\Mail\Mailable;

/**
 * Website activated email to RTD notification.
 */
class RTDWebsiteActivatedEmail extends RTDEmailNotification
{
    /**
     * The activated website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Default constructor.
     *
     * @param Website $website the activated website
     */
    public function __construct(Website $website)
    {
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
        return new RTDWebsiteActivated($notifiable, $this->website);
    }
}
