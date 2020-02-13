<?php

namespace App\Notifications;

use App\Enums\PublicAdministrationStatus;
use App\Mail\RTDEmailAddressChanged;
use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Mail\Mailable;

class RTDEmailAddressChangedEmail extends RTDEmailNotification
{
    protected function buildEmail($notifiable): Mailable
    {
        return new RTDEmailAddressChanged($notifiable, $this->getEarliestRegisteredAdministrator($notifiable));
    }

    protected function getEarliestRegisteredAdministrator(PublicAdministration $notifiable): User
    {
        if ($notifiable->status->is(PublicAdministrationStatus::PENDING)) {
            return $notifiable->getAdministrators()->first();
        }
        $administrators = $notifiable->getActiveAdministrators();

        return $administrators->where('created_at', $administrators->min('created_at'))->first();
    }
}
