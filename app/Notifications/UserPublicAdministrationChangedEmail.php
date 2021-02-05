<?php

namespace App\Notifications;

use App\Mail\UserEmailForPublicAdministrationChanged;
use App\Models\PublicAdministration;
use Illuminate\Mail\Mailable;

/**
 * User reactivated email notification.
 */
class UserPublicAdministrationChangedEmail extends UserEmailNotification
{
    /**
     * The updated email address.
     *
     * @var string the updated email address
     */
    protected $updatedEmail;

    /**
     * Notification constructor.
     *
     * @param PublicAdministration $publicAdministration the public administration
     * @param string $recipientEmail the email address to use for thins notification
     * @param string $updatedEmail the updated email address
     */
    public function __construct(PublicAdministration $publicAdministration = null, string $recipientEmail = null, string $updatedEmail)
    {
        parent::__construct($publicAdministration, $recipientEmail);
        $this->updatedEmail = $updatedEmail;
    }

    /**
     * Initialize the mail message.
     *
     * @param mixed $notifiable the target
     *
     * @return Mailable the mail message
     */
    protected function buildEmail($notifiable): Mailable
    {
        return new UserEmailForPublicAdministrationChanged($notifiable, $this->publicAdministration, $this->updatedEmail);
    }
}
