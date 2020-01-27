<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\UserInvitedEmail;

trait SendsNotificationsToPublicAdministrationAdmin
{
    public function sendUserInvitedNotificationToAdministrators(User $user): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($user) {
            $administrator->notify(new UserInvitedEmail($user, $this));
        });
    }
}
