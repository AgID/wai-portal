<?php

namespace App\Notifications;

use App\Mail\UserWebsiteUnarchived;
use App\Models\Website;
use Illuminate\Mail\Mailable;

/**
 * Website unarchived email notification to public administration administrators.
 */
class UserWebsiteUnarchivedEmail extends UserEmailNotification
{
    /**
     * The unarchived website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Default constructor.
     *
     * @param Website $website the unarchived website
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
        return new UserWebsiteUnarchived($notifiable, $this->website);
    }
}
