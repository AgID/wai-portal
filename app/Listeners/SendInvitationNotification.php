<?php

namespace App\Listeners;

use App\Events\Auth\UserInvited;
use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class SendInvitationNotification
{
    /**
     * Handle the event.
     *
     * @param UserInvited $event
     *
     * @return void
     */
    public function handle(UserInvited $event)
    {
        if ($event->user instanceof MustVerifyEmail && !$event->user->hasVerifiedEmail()) {
            $event->user->notify(new VerifyEmail($event->publicAdministration, $event->invitedBy));
        }
    }
}
