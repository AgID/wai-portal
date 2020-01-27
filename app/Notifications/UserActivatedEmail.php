<?php

namespace App\Notifications;

use App\Mail\UserActivated;
use App\Models\User;
use Illuminate\Mail\Mailable;

class UserActivatedEmail extends UserEmailNotification
{
    private $activatedUser;

    public function __construct(User $activatedUser)
    {
        $this->activatedUser = $activatedUser;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new UserActivated($notifiable, $this->activatedUser);
    }
}
