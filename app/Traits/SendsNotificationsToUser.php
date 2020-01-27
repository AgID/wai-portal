<?php

namespace App\Traits;

use App\Models\PublicAdministration;
use App\Notifications\VerifyEmail;

trait SendsNotificationsToUser
{
    /**
     * Configure information for notifications over mail channel.
     *
     * @param PublicAdministration|null $publicAdministration the public administration the user belongs to or null if user is registering a new Public Administration
     */
    public function sendEmailVerificationNotification(?PublicAdministration $publicAdministration = null): void
    {
        $this->notify(new VerifyEmail($publicAdministration));
    }
}
