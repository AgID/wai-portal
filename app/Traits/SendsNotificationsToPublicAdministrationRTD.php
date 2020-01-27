<?php

namespace App\Traits;

use App\Notifications\RTDPublicAdministrationRegisteredEmail;

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
}
