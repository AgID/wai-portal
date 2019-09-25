<?php

namespace App\Notifications;

use App\Mail\UserWebsiteArchived;
use App\Models\User;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Website archived user notification.
 */
class WebsiteArchivedUserEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The archived website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Manual flag.
     *
     * @var bool wether the website was archived manually
     */
    protected $manual;

    /**
     * Notification constructor.
     *
     * @param Website $website the website
     * @param bool $manual wether the website was archived manually
     */
    public function __construct(Website $website, bool $manual)
    {
        $this->website = $website;
        $this->manual = $manual;
    }

    /**
     * Notification channels.
     *
     * @param User $notifiable the user
     *
     * @return array the channels
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the message.
     *
     * @param User $notifiable the user
     *
     * @return UserWebsiteArchived the mail message
     */
    public function toMail(User $notifiable): UserWebsiteArchived
    {
        return (new UserWebsiteArchived($notifiable, $this->website, $this->manual))->to($notifiable->email, $notifiable->full_name);
    }
}
