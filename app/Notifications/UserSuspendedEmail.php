<?php

namespace App\Notifications;

use App\Mail\UserSuspended;
use App\Models\User;
use Illuminate\Mail\Mailable;

class UserSuspendedEmail extends UserEmailNotification
{
    protected $suspendedUser;

    public function __construct(User $suspendedUser)
    {
        $this->suspendedUser = $suspendedUser;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new UserSuspended($notifiable, $this->suspendedUser);
    }
}
