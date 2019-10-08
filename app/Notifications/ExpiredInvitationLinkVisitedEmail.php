<?php

namespace App\Notifications;

use App\Mail\ExpiredInvitationLinkVisited;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Expired URL visited notification.
 */
class ExpiredInvitationLinkVisitedEmail extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The user the expired URL refers to.
     *
     * @var User the user
     */
    protected $user;

    /**
     * Default constructor.
     *
     * @param User $user the user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
     * @return ExpiredInvitationLinkVisited the mail message
     */
    public function toMail(User $notifiable): ExpiredInvitationLinkVisited
    {
        return (new ExpiredInvitationLinkVisited($notifiable, $this->user))->to($notifiable->email, $notifiable->full_name);
    }
}
