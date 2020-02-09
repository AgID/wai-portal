<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

/**
 * User email notification.
 */
abstract class EmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Notification channels.
     *
     * @param mixed $notifiable the notification target
     *
     * @return array the channels notification channels
     */
    final public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the message.
     *
     * @param mixed $notifiable the target
     *
     * @return Mailable the mailable
     */
    abstract public function toMail($notifiable): Mailable;

    /**
     * Initialize the mail message.
     *
     * @param mixed $notifiable the target
     *
     * @return Mailable the mail message
     */
    abstract protected function buildEmail($notifiable): Mailable;
}
