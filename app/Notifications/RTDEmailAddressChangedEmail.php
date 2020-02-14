<?php

namespace App\Notifications;

use App\Enums\PublicAdministrationStatus;
use App\Mail\RTDEmailAddressChanged;
use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Mail\Mailable;

/**
 * RTD email address change email to new RTD notification.
 */
class RTDEmailAddressChangedEmail extends RTDEmailNotification
{
    /**
     * Initialize the mail message.
     *
     * @param mixed $notifiable the target
     *
     * @return Mailable the mail message
     */
    protected function buildEmail($notifiable): Mailable
    {
        return new RTDEmailAddressChanged($notifiable, $this->getEarliestRegisteredAdministrator($notifiable));
    }

    /**
     * Get the oldest active administrator for the given public administration.
     *
     * @param PublicAdministration $notifiable the public administration
     *
     * @return User the oldest administrator
     */
    protected function getEarliestRegisteredAdministrator(PublicAdministration $notifiable): User
    {
        if ($notifiable->status->is(PublicAdministrationStatus::PENDING)) {
            return $notifiable->getAdministrators()->first();
        }
        $administrators = $notifiable->getActiveAdministrators();

        return $administrators->where('created_at', $administrators->min('created_at'))->first();
    }
}
