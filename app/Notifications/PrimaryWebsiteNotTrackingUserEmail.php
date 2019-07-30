<?php

namespace App\Notifications;

use App\Mail\UserPrimaryWebsiteNotTracking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Primary website not tracking user notification.
 */
class PrimaryWebsiteNotTrackingUserEmail extends Notification implements ShouldQueue
{
    use Queueable;

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
     * @return UserPrimaryWebsiteNotTracking the mail message
     */
    public function toMail(User $notifiable): UserPrimaryWebsiteNotTracking
    {
        return (new UserPrimaryWebsiteNotTracking($notifiable))->to($notifiable->email, $notifiable->full_name);
    }
}
