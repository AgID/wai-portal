<?php

namespace App\Notifications;

use App\Mail\UserPrimaryWebsiteNotTracking;
use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PrimaryWebsiteNotTrackingUserEmail extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(PublicAdministration $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): UserPrimaryWebsiteNotTracking
    {
        return (new UserPrimaryWebsiteNotTracking($notifiable))->to($notifiable->email, $notifiable->full_name);
    }
}
