<?php

namespace App\Traits;

use App\Enums\WebsiteType;
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
     * Send public administration registered notification.
     */
    public function sendPublicAdministrationRegisteredNotificationToRTD(): void
    {
        $registeringUser = $this->users()->first();

        //NOTE: don't send notification to RTD
        //      if he/she is the PA registering user
        if (($registeringUser->email !== $this->rtd_mail) && $this->sendNotificationOnCurrentEnvironment()) {
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
        if ($this->sendNotificationOnCurrentEnvironment()) {
            $this->notify(new RTDWebsiteActivatedEmail($website));
        }
    }

    /**
     * Send public administration RTD email changed.
     */
    public function sendPublicAdministrationUpdatedRTD(): void
    {
        if ($this->sendNotificationOnCurrentEnvironment()) {
            $this->notify(new RTDEmailAddressChangedEmail());
        }
    }

    /**
     * Check the current environment.
     * On public-playground don't send notification in public adminitration is from IPA.
     */
    private function sendNotificationOnCurrentEnvironment()
    {
        if (!app()->environment('public-playground')) {
            return true;
        }

        if (!config('wai.custom_public_administrations', false)) {
            return true;
        }

        $websiteType = WebsiteType::coerce(WebsiteType::INSTITUTIONAL_PLAY);
        $websiteTypeDescription = ucfirst($websiteType->description);
        if ($this->type === $websiteTypeDescription) {
            return true;
        }

        return false;
    }
}
