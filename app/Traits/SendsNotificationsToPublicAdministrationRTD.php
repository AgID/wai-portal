<?php

namespace App\Traits;

use App\Models\Website;
use App\Notifications\RTDEmailAddressChangedEmail;
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
        //NOTE: don't send notification to RTD
        //      if he/she is the PA registering user
        if ($this->users()->first()->email !== $this->rtd_mail) {
            $this->notify(new RTDPublicAdministrationRegisteredEmail());
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

    public function sendPublicAdministrationUpdatedRTD(): void
    {
        $this->notify(new RTDEmailAddressChangedEmail());
    }
}
