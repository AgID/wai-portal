<?php

namespace App\Traits;

use App\Models\Website;
use App\Notifications\RTDPublicAdministrationRegisteredEmail;
use App\Notifications\RTDWebsiteActivatedEmail;

trait SendsNotificationsToPublicAdministrationRTD
{
    public function sendPublicAdministrationRegisteredNotificationToRTD(): void
    {
        //NOTE: don't send notification to RTD
        //      if he/she is the PA registering user
        if ($this->users()->first()->email !== $this->rtd_mail) {
            $this->notify(new RTDPublicAdministrationRegisteredEmail());
        }
    }

    public function sendWebsiteActivatedNotificationToRTD(Website $website): void
    {
        $this->notify(new RTDWebsiteActivatedEmail($website));
    }
}
