<?php

namespace App\Notifications;

use App\Mail\UserWebsiteAdded;
use App\Models\Website;
use Illuminate\Mail\Mailable;

/**
 * Website added email notification to public administration administrators.
 */
class UserWebsiteAddedEmail extends UserEmailNotification
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
        return new UserWebsiteAdded($notifiable, $this->website);
    }
}
