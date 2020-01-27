<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Notification;

abstract class EmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    final public function via($notifiable)
    {
        return ['mail'];
    }

    abstract public function toMail($notifiable): Mailable;

    abstract protected function buildEmail($notifiable): Mailable;
}
