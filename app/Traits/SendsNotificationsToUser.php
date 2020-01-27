<?php

namespace App\Traits;

use App\Models\PublicAdministration;
use App\Models\Website;
use App\Notifications\ActivatedEmail;
use App\Notifications\PasswordChangedEmail;
use App\Notifications\PasswordResetRequestEmail;
use App\Notifications\PublicAdministrationRegisteredEmail;
use App\Notifications\ReactivatedEmail;
use App\Notifications\SuspendedEmail;
use App\Notifications\VerifyEmail;
use App\Notifications\WebsiteAddedEmail;

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

    public function sendPasswordChangedNotification(): void
    {
        $this->notify(new PasswordChangedEmail());
    }

    public function sendPasswordResetRequestNotification(string $token): void
    {
        $this->notify(new PasswordResetRequestEmail($token));
    }

    public function sendActivatedNotification(): void
    {
        $this->notify(new ActivatedEmail());
    }

    public function sendSuspendedNotification(): void
    {
        $this->notify(new SuspendedEmail());
    }

    public function sendReactivatedNotification(): void
    {
        $this->notify(new ReactivatedEmail());
    }

    public function sendPublicAdministrationRegisteredNotification(PublicAdministration $publicAdministration): void
    {
        $this->notify(new PublicAdministrationRegisteredEmail($publicAdministration));
    }

    public function sendWebsiteAddedNotification(Website $website): void
    {
        $this->notify(new WebsiteAddedEmail($website));
    }
}
