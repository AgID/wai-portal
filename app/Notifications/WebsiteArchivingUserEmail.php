<?php

namespace App\Notifications;

use App\Mail\UserWebsiteArchiving;
use App\Models\User;
use App\Models\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class WebsiteArchivingUserEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $website;

    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): UserWebsiteArchiving
    {
        return (new UserWebsiteArchiving($notifiable, $this->website))->to($notifiable->email, $notifiable->full_name);
    }
}
