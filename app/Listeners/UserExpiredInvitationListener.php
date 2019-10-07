<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserInvitationLinkExpired;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserExpiredInvitationListener implements ShouldQueue
{
    public function handle(UserInvitationLinkExpired $event): void
    {
        $user = $event->getUser();
        if ($user->status->is(UserStatus::INVITED) && (!$user->isA(UserRole::SUPER_ADMIN))) {
            $publicAdministration = $user->publicAdministrations()->first();
            $publicAdministration->getActiveAdministrators()->each(function ($administrator) use ($user) {
                $administrator->sendExpiredInvitationLinkVisited($user);
            });

            logger()->info(
                'User ' . $user->uuid . ' tried to use an expired invitation link.',
                [
                    'user' => $user->uuid,
                    'pa' => $publicAdministration->ipa_code,
                    'event' => EventType::EXPIRED_USER_INVITATION_USED,
                ]
            );
        }
    }
}
