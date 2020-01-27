<?php

namespace App\Notifications;

use App\Mail\UserWebsiteAccessChanged;
use App\Models\User;
use Illuminate\Mail\Mailable;

class UserWebsiteAccessChangedEmail extends UserEmailNotification
{
    protected $modifiedUser;

    public function __construct(User $modifiedUser)
    {
        $this->modifiedUser = $modifiedUser;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new UserWebsiteAccessChanged($notifiable, $this->modifiedUser);
    }
}
