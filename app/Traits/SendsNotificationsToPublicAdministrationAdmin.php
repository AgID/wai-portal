<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Website;
use App\Notifications\UserActivatedEmail;
use App\Notifications\UserInvitedEmail;
use App\Notifications\UserReactivatedEmail;
use App\Notifications\UserSuspendedEmail;
use App\Notifications\UserWebsiteActivatedEmail;
use App\Notifications\UserWebsiteAddedEmail;

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

    public function sendWebsiteAddedNotificationToAdministrators(Website $website, User $user): void
    {
        $this->getActiveAdministrators()->except([$user->id])->each(function (User $administrator) use ($website) {
            $administrator->notify(new UserWebsiteAddedEmail($website));
        });
    }

    public function sendWebsiteActivatedNotificationToAdministrators(Website $website): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website) {
            if ($administrator->email !== $this->rtd_mail) {
                $administrator->notify(new UserWebsiteActivatedEmail($website));
            }
        });
    }
}
