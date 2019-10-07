<?php

namespace App\Notifications;

use App\Mail\ExpiredInvitationLinkVisited;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ExpiredInvitationLinkVisitedEmail extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): ExpiredInvitationLinkVisited
    {
        return (new ExpiredInvitationLinkVisited($notifiable, $this->user))->to($notifiable->email, $notifiable->full_name);
    }
}
