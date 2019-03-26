<?php

namespace App\Listeners;

use App\Events\Auth\Invited;
use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class SendInvitationNotification
{
    /**
     * Handle the event.
     *
     * @param Invited $event
     *
     * @return void
     */
    public function handle(Invited $event)
    {
        if ($event->user instanceof MustVerifyEmail && !$event->user->hasVerifiedEmail()) {
            $event->user->notify(new VerifyEmail($event->publicAdministration, $event->invitedBy));
        }
    }
}
