<?php

namespace App\Notifications;

use App\Mail\UserWebsitePurged;
use App\Models\PublicAdministration;
use Illuminate\Mail\Mailable;

/**
 * Website purged email notification to public administration administrators.
 */
class UserWebsitePurgedEmail extends UserEmailNotification
{
    /**
     * The purged website.
     *
     * @var mixed the website
     */
    protected $website;

    /**
     * Default constructor.
     *
     * @param mixed $website the purged website
     * @param PublicAdministration $publicAdministration the public administration
     */
    public function __construct($website, PublicAdministration $publicAdministration)
    {
        parent::__construct($publicAdministration);
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
        return new UserWebsitePurged($notifiable, $this->website);
    }
}
