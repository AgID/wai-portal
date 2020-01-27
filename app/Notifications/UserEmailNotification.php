<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Mail\Mailable;

/**
 * User email notification template.
 */
abstract class UserEmailNotification extends EmailNotification
{
    /**
     * Build the message.
     *
     * @param User $notifiable the target
     *
     * @return Mailable the mailable
     */
    public function toMail($notifiable): Mailable
    {
        return $this->buildEmail($notifiable)->to($notifiable->email, ($notifiable->full_name !== $notifiable->fiscal_number ? $notifiable->full_name : null));
    }
}
