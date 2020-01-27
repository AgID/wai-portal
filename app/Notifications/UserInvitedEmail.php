<?php

namespace App\Notifications;

use App\Mail\UserInvited;
use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Mail\Mailable;

class UserInvitedEmail extends UserEmailNotification
{
    protected $invitedUser;

    protected $publicAdministration;

    public function __construct(User $invitedUser, PublicAdministration $publicAdministration)
    {
        $this->invitedUser = $invitedUser;
        $this->publicAdministration = $publicAdministration;
    }

    protected function buildEmail($notifiable): Mailable
    {
        return new UserInvited($notifiable, $this->invitedUser, $this->publicAdministration);
    }
}
