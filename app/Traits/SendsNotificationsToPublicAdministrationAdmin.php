<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Website;
use App\Notifications\UserActivatedEmail;
use App\Notifications\UserInvitedEmail;
use App\Notifications\UserPrimaryWebsiteNotTrackingEmail;
use App\Notifications\UserReactivatedEmail;
use App\Notifications\UserSuspendedEmail;
use App\Notifications\UserWebsiteAccessChangedEmail;
use App\Notifications\UserWebsiteActivatedEmail;
use App\Notifications\UserWebsiteAddedEmail;
use App\Notifications\UserWebsiteArchivedEmail;
use App\Notifications\UserWebsiteArchivingEmail;
use App\Notifications\UserWebsitePurgedEmail;
use App\Notifications\UserWebsitePurgingEmail;
use App\Notifications\UserWebsiteUnarchivedEmail;
use App\Notifications\UserWebsiteUrlChangedEmail;

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

    public function sendWebsiteArchivedNotificationToAdministrators(Website $website, bool $manually): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website, $manually) {
            $administrator->notify(new UserWebsiteArchivedEmail($website, $manually));
        });
    }

    public function sendWebsiteUnarchivedNotificationToAdministrators(Website $website): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website) {
            $administrator->notify(new UserWebsiteUnarchivedEmail($website));
        });
    }

    public function sendPrimaryWebsiteNotTrackingNotificationToAdministrators(): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) {
            $administrator->notify(new UserPrimaryWebsiteNotTrackingEmail());
        });
    }

    public function sendWebsiteArchivingNotificationToAdministrators(Website $website, int $daysLeft): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website, $daysLeft) {
            $administrator->notify(new UserWebsiteArchivingEmail($website, $daysLeft));
        });
    }

    public function sendWebsitePurgingNotificationToAdministrators(Website $website): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website) {
            $administrator->notify(new UserWebsitePurgingEmail($website));
        });
    }

    public function sendWebsitePurgedNotificationToAdministrators($website): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website) {
            $administrator->notify(new UserWebsitePurgedEmail($website));
        });
    }

    public function sendWebsiteUrlChangedNotificationToAdministrators($website): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website) {
            $administrator->notify(new UserWebsiteUrlChangedEmail($website));
        });
    }

    public function sendWebsiteAccessChangedNotificationToAdministrators(User $user): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($user) {
            $administrator->notify(new UserWebsiteAccessChangedEmail($user));
        });
    }
}
