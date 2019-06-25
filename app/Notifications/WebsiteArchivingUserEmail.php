<?php

namespace App\Notifications;

use App\Mail\UserWebsiteArchiving;
use App\Models\User;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Website scheduled for archiving user notification.
 */
class WebsiteArchivingUserEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Number of days remaining before archiving.
     *
     * @var int the number of days
     */
    protected $daysLeft;

    /**
     * Notification constructor.
     *
     * @param Website $website the website
     * @param int $daysLeft the remaining days
     */
    public function __construct(Website $website, int $daysLeft)
    {
        $this->website = $website;
        $this->daysLeft = $daysLeft;
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
     * @return UserWebsiteArchiving the mail message
     */
    public function toMail(User $notifiable): UserWebsiteArchiving
    {
        return (new UserWebsiteArchiving($notifiable, $this->website, $this->daysLeft))->to($notifiable->email, $notifiable->full_name);
    }
}
