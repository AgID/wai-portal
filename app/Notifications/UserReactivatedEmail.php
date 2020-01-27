<?php

namespace App\Notifications;

use App\Mail\UserReactivated;
use App\Models\User;
use Illuminate\Mail\Mailable;

class UserReactivatedEmail extends UserEmailNotification
{
    protected $reactivatedUser;

    public function __construct(User $reactivatedUser)
    {
        $this->reactivatedUser = $reactivatedUser;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new UserReactivated($notifiable, $this->reactivatedUser);
    }
}
