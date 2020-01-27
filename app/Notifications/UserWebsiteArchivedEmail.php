<?php

namespace App\Notifications;

use App\Mail\UserWebsiteArchived;
use App\Models\User;
use App\Models\Website;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

/**
 * Website archived user notification.
 */
class UserWebsiteArchivedEmail extends UserEmailNotification
{
    /**
     * The archived website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Manual flag.
     *
     * @var bool whether the website was archived manually
     */
    protected $manually;

    /**
     * Notification constructor.
     *
     * @param Website $website the website
     * @param bool $manual wether the website was archived manually
     */
    public function __construct(Website $website, bool $manually)
    {
        $this->website = $website;
        $this->manually = $manually;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new UserWebsiteArchived($notifiable, $this->website, $this->manually);
    }
}
