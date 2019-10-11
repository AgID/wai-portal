<?php

namespace App\Listeners;

use App\Events\User\UserEmailChanged;
use App\Events\User\UserStatusChanged;
use App\Events\User\UserUpdated;
use App\Events\User\UserUpdating;
use Illuminate\Events\Dispatcher;

/**
 * User model events subscriber.
 */
class UserUpdatesSubscriber
{
    /**
     * Handle user model updating events.
     *
     * @param UserUpdating $event the event
     */
    public function onUpdating(UserUpdating $event): void
    {
        $user = $event->getUser();
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
    }

    /**
     * Handle user model updated events.
     *
     * @param UserUpdated $event the event
     *
     * @throws \App\Exceptions\AnalyticsServiceAccountException if the Analytics Service account doesn't exist
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind analytics service
     */
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

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events the dispatcher
     */
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
