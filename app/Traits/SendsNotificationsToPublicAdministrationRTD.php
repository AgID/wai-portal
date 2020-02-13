<?php

namespace App\Traits;

use App\Models\Website;
use App\Notifications\RTDPublicAdministrationRegisteredEmail;
use App\Notifications\RTDWebsiteActivatedEmail;

/**
 * Notifications to public administration RTD management.
 */
trait SendsNotificationsToPublicAdministrationRTD
{
    /**
     * Send public administration registere notification.
     */
    public function sendPublicAdministrationRegisteredNotificationToRTD(): void
    {
        $registeringUser = $this->users()->first();

        //NOTE: don't send notification to RTD
        //      if he/she is the PA registering user
        if ($registeringUser->email !== $this->rtd_mail) {
            $this->notify(new RTDPublicAdministrationRegisteredEmail($registeringUser));
        }
    }

    /**
     * Send website activated notification.
     *
     * @param Website $website the activated website
     */
    public function sendWebsiteActivatedNotificationToRTD(Website $website): void
    {
        $this->notify(new RTDWebsiteActivatedEmail($website));
    }
}
