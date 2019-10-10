<?php

namespace App\Listeners;

use App\Enums\UserStatus;
use App\Events\User\UserEmailChanged;
use App\Events\User\UserStatusChanged;
use App\Events\User\UserUpdated;
use App\Events\User\UserUpdating;

class UserUpdatesSubscriber
{
    public function onUpdating(UserUpdating $event): void
    {
        $user = $event->getUser();
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
    }

    public function onUpdated(UserUpdated $event): void
    {
        $user = $event->getUser();
        if (!empty($user->email_verified_at) && $user->isDirty('email_verified_at') && $user->hasAnalyticsServiceAccount()) {
            $user->updateAnalyticsServiceAccountEmail();
        }

        if ($user->isDirty('email')) {
            event(new UserEmailChanged($user));
        }

        if ($user->isDirty('status')) {
            event(new UserStatusChanged($user, $user->getOriginal('status')));
        }
    }

    public function subscribe($events): void
    {
        $events->listen(
            'App\Events\User\UserUpdating',
            'App\Listeners\UserUpdatesSubscriber@onUpdating'
        );

        $events->listen(
            'App\Events\User\UserUpdated',
            'App\Listeners\UserUpdatesSubscriber@onUpdated'
        );
    }
}
