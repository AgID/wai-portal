<?php

namespace App\Notifications;

use App\Mail\UserWebsiteActivated;
use App\Models\Website;
use Illuminate\Mail\Mailable;

/**
 * Website activated email notification to public administration administrators.
 */
class UserWebsiteActivatedEmail extends UserEmailNotification
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
        return new UserWebsiteActivated($notifiable, $this->website);
    }
}
