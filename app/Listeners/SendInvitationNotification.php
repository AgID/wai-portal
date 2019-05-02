<?php

namespace App\Listeners;

use App\Events\User\UserInvited;
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
        if ($event->getUser() instanceof MustVerifyEmail && !$event->getUser()->hasVerifiedEmail()) {
            $event->getUser()->notify(new VerifyEmail($event->getPublicAdministration(), $event->getInvitedBy()));
        }
    }
}
