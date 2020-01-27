<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\UserActivatedEmail;
use App\Notifications\UserInvitedEmail;
use App\Notifications\UserReactivatedEmail;
use App\Notifications\UserSuspendedEmail;

trait SendsNotificationsToPublicAdministrationAdmin
{
    public function sendUserActivatedNotificationToAdministrators(User $user): void
    {
        $this->getActiveAdministrators()->except([$user->id])->each(function (User $administrator) use ($user) {
            $administrator->notify(new UserActivatedEmail($user));
        });
    }

    public function sendUserInvitedNotificationToAdministrators(User $user): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($user) {
            $administrator->notify(new UserInvitedEmail($user, $this));
        });
    }

    public function sendUserSuspendedNotificationToAdministrators(User $user): void
    {
        $this->getActiveAdministrators()->except([$user->id])->each(function (User $administrator) use ($user) {
            $administrator->notify(new UserSuspendedEmail($user));
        });
    }

    public function sendUserReactivatedNotificationToAdministrators(User $user): void
    {
        $this->getActiveAdministrators()->except([$user->id])->each(function (User $administrator) use ($user) {
            $administrator->notify(new UserReactivatedEmail($user));
        });
    }
}
