<?php

namespace App\Traits;

use App\Models\User;
use App\Models\Website;
use App\Notifications\UserActivatedEmail;
use App\Notifications\UserExpiredInvitationLinkVisitedEmail;
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

/**
 * Notifications to public administration administrators management.
 */
trait SendsNotificationsToPublicAdministrationAdmin
{
    /**
     * Send user activated notification.
     *
     * @param User $user the activated user
     */
    public function sendUserActivatedNotificationToAdministrators(User $user): void
    {
        $this->getActiveAdministrators()->except([$user->id])->each(function (User $administrator) use ($user) {
            $administrator->notify(new UserActivatedEmail($user));
        });
    }

    /**
     * Send user invited notification.
     *
     * @param User $user the invited user
     */
    public function sendUserInvitedNotificationToAdministrators(User $user): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($user) {
            $administrator->notify(new UserInvitedEmail($user, $this));
        });
    }

    /**
     * Send user suspended notification.
     *
     * @param User $user the suspended user
     */
    public function sendUserSuspendedNotificationToAdministrators(User $user): void
    {
        $this->getActiveAdministrators()->except([$user->id])->each(function (User $administrator) use ($user) {
            $administrator->notify(new UserSuspendedEmail($user));
        });
    }

    /**
     * Send user reactivated notification.
     *
     * @param User $user the reactivated user
     */
    public function sendUserReactivatedNotificationToAdministrators(User $user): void
    {
        $this->getActiveAdministrators()->except([$user->id])->each(function (User $administrator) use ($user) {
            $administrator->notify(new UserReactivatedEmail($user));
        });
    }

    /**
     * Send website added notification.
     *
     * @param Website $website the added website
     * @param User $user the user who added the website
     */
    public function sendWebsiteAddedNotificationToAdministrators(Website $website, User $user): void
    {
        $this->getActiveAdministrators()->except([$user->id])->each(function (User $administrator) use ($website) {
            $administrator->notify(new UserWebsiteAddedEmail($website));
        });
    }

    /**
     * Send website activated notification.
     *
     * @param Website $website the activated website
     */
    public function sendWebsiteActivatedNotificationToAdministrators(Website $website): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website) {
            if ($administrator->email !== $this->rtd_mail) {
                $administrator->notify(new UserWebsiteActivatedEmail($website));
            }
        });
    }

    /**
     * Send website archived notification.
     *
     * @param Website $website the archived website
     * @param bool $manually if the website was manually archived
     */
    public function sendWebsiteArchivedNotificationToAdministrators(Website $website, bool $manually): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website, $manually) {
            $administrator->notify(new UserWebsiteArchivedEmail($website, $manually));
        });
    }

    /**
     * Send website unarchived notification.
     *
     * @param Website $website the unarchived website
     */
    public function sendWebsiteUnarchivedNotificationToAdministrators(Website $website): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website) {
            $administrator->notify(new UserWebsiteUnarchivedEmail($website));
        });
    }

    /**
     * Send primary website not tracking notification.
     */
    public function sendPrimaryWebsiteNotTrackingNotificationToAdministrators(): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) {
            $administrator->notify(new UserPrimaryWebsiteNotTrackingEmail());
        });
    }

    /**
     * Send website archiving notification.
     *
     * @param Website $website the website scheduled for archiving
     * @param int $daysLeft the number of days left before automatically archive
     */
    public function sendWebsiteArchivingNotificationToAdministrators(Website $website, int $daysLeft): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website, $daysLeft) {
            $administrator->notify(new UserWebsiteArchivingEmail($website, $daysLeft));
        });
    }

    /**
     * Send website purging notification.
     *
     * @param Website $website the website scheduled for purging
     */
    public function sendWebsitePurgingNotificationToAdministrators(Website $website): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website) {
            $administrator->notify(new UserWebsitePurgingEmail($website));
        });
    }

    /**
     * Send website purged notification.
     *
     * @param mixed $website the purged website
     */
    public function sendWebsitePurgedNotificationToAdministrators($website): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website) {
            $administrator->notify(new UserWebsitePurgedEmail($website));
        });
    }

    /**
     * Send website URL changed notification.
     *
     * @param Website $website the website
     */
    public function sendWebsiteUrlChangedNotificationToAdministrators(Website $website): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($website) {
            $administrator->notify(new UserWebsiteUrlChangedEmail($website));
        });
    }

    /**
     * Send website access for user changed notification.
     *
     * @param User $user the user whose permissions changed
     */
    public function sendWebsiteAccessChangedNotificationToAdministrators(User $user): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($user) {
            $administrator->notify(new UserWebsiteAccessChangedEmail($user));
        });
    }

    /**
     * Send expired invitation link used notification.
     *
     * @param User $invitedUser the invited user
     */
    public function sendExpiredInvitationLinkVisitedToAdministrators(User $invitedUser): void
    {
        $this->getActiveAdministrators()->each(function (User $administrator) use ($invitedUser) {
            $administrator->notify(new UserExpiredInvitationLinkVisitedEmail($invitedUser));
        });
    }
}
