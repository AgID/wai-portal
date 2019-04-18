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
     * The activated website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Notification constructor.
     *
     * @param Website $website the website
     */
    public function __construct(Website $website)
    {
        $this->website = $website;
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
        return (new UserWebsiteArchiving($notifiable, $this->website))->to($notifiable->email, $notifiable->full_name);
    }
}
