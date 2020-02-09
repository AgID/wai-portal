<?php

namespace App\Listeners;

use App\Events\User\UserInvited;
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
        $user = $event->getUser();
        if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification($event->getPublicAdministration());
        }
    }
}
