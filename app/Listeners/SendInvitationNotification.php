<?php

namespace App\Listeners;

use App\Events\User\UserInvited;
use App\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * User invitation events listener.
 */
class SendInvitationNotification implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param UserInvited $event
     */
    public function handle(UserInvited $event): void
    {
        if ($event->getUser() instanceof MustVerifyEmail && !$event->getUser()->hasVerifiedEmail()) {
            $event->getUser()->notify(new VerifyEmail($event->getPublicAdministration(), $event->getInvitedBy()));
        }
    }
}
