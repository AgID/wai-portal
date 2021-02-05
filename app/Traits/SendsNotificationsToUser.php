<?php

namespace App\Traits;

use App\Models\PublicAdministration;
use App\Models\Website;
use App\Notifications\ActivatedEmail;
use App\Notifications\DeletedEmail;
use App\Notifications\PasswordChangedEmail;
use App\Notifications\PasswordResetRequestEmail;
use App\Notifications\PublicAdministrationActivatedEmail;
use App\Notifications\PublicAdministrationPurgedEmail;
use App\Notifications\PublicAdministrationRegisteredEmail;
use App\Notifications\ReactivatedEmail;
use App\Notifications\SuspendedEmail;
use App\Notifications\UserPublicAdministrationChangedEmail;
use App\Notifications\UserPublicAdministrationInvitedEmail;
use App\Notifications\VerifyEmail;
use App\Notifications\WebsiteAddedEmail;

/**
 * Notifications to user management.
 */
trait SendsNotificationsToUser
{
    /**
     * Send email verification notification.
     *
     * @param PublicAdministration|null $publicAdministration the public administration the user belongs to or null if user is registering a new Public Administration
     */
    public function sendEmailVerificationNotification(?PublicAdministration $publicAdministration = null): void
    {
        $this->notify(new VerifyEmail($publicAdministration));
    }

    /**
     * Send password reset completed notification.
     */
    public function sendPasswordChangedNotification(): void
    {
        $this->notify(new PasswordChangedEmail());
    }

    /**
     * Send password reset request notification.
     *
     * @param string $token the password reset token
     */
    public function sendPasswordResetRequestNotification(string $token): void
    {
        $this->notify(new PasswordResetRequestEmail($token));
    }

    /**
     * Send user activated notification.
     *
     * @param PublicAdministration|null $publicAdministration the public administration the user belongs to or null if user is registering a new public administration
     */
    public function sendActivatedNotification(?PublicAdministration $publicAdministration = null): void
    {
        $this->notify(new ActivatedEmail($publicAdministration));
    }

    /**
     * Send user suspended notification.
     *
     * @param PublicAdministration $publicAdministration the public administration the suspended user belongs to
     */
    public function sendSuspendedNotification(PublicAdministration $publicAdministration): void
    {
        $this->notify(new SuspendedEmail($publicAdministration));
    }

    /**
     * Send user deleted notification.
     *
     * @param PublicAdministration $publicAdministration the public administration the deleted user belongs to
     */
    public function sendDeletededNotification(PublicAdministration $publicAdministration): void
    {
        $this->notify(new DeletedEmail($publicAdministration));
    }

    /**
     * Send user reactivated notification.
     *
     * @param PublicAdministration $publicAdministration the public administration the reactivated user belongs to
     */
    public function sendReactivatedNotification(PublicAdministration $publicAdministration): void
    {
        $this->notify(new ReactivatedEmail($publicAdministration));
    }

    /**
     * Send user suspended notification.
     *
     * @param PublicAdministration $publicAdministration the public administration the suspended user belongs to
     * @param string $recipientEmail the email address to use for the notification
     * @param string $updatedEmail the updated email address
     */
    public function sendEmailPublicAdministrationChangedNotification(PublicAdministration $publicAdministration, string $recipientEmail, string $updatedEmail): void
    {
        $this->notify(new UserPublicAdministrationChangedEmail($publicAdministration, $recipientEmail, $updatedEmail));
    }

    /**
     * Send public administration registered notification.
     *
     * @param PublicAdministration $publicAdministration the registered public administration
     */
    public function sendPublicAdministrationRegisteredNotification(PublicAdministration $publicAdministration): void
    {
        $this->notify(new PublicAdministrationRegisteredEmail($publicAdministration));
    }

    /**
     * Send website added notification.
     *
     * @param Website $website the added website
     */
    public function sendWebsiteAddedNotification(Website $website): void
    {
        $this->notify(new WebsiteAddedEmail($website));
    }

    /**
     * Send public administration purged notification.
     *
     * @param mixed $publicAdministration the purged public administration
     */
    public function sendPublicAdministrationPurgedNotification($publicAdministration, $userEmailForPurgedPublicAdministration): void
    {
        $this->notify(new PublicAdministrationPurgedEmail($publicAdministration, $userEmailForPurgedPublicAdministration));
    }

    /**
     * Send public administration activated notification.
     *
     * @param PublicAdministration $publicAdministration the activated public administration
     */
    public function sendPublicAdministrationActivatedNotification(PublicAdministration $publicAdministration): void
    {
        $this->notify(new PublicAdministrationActivatedEmail($publicAdministration));
    }

    /**
     * Send public administration invited notification.
     *
     * @param PublicAdministration $publicAdministration the public administration the user is invited to
     */
    public function sendPublicAdministrationInvitedNotification(PublicAdministration $publicAdministration): void
    {
        $this->notify(new UserPublicAdministrationInvitedEmail($publicAdministration));
    }
}
