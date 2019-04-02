<?php

namespace App\Listeners;

class EventToLogSubscriber
{
    /**
     * Handle user registration events.
     */
    public function onRegistered($event)
    {
        logger()->info('New user registered: ' . $event->user->getInfo()); //TODO: notify me!
    }

    /**
     * Handle user invitation events.
     */
    public function onInvited($event)
    {
        logger()->info('New user invited: ' . $event->user->getInfo() . ' by ' . $event->invitedBy->getInfo()); //TODO: notify me!
    }

    /**
     * Handle user verification events.
     */
    public function onVerified($event)
    {
        logger()->info('User ' . $event->user->getInfo() . ' confirmed email address.'); //TODO: notify me!
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'Illuminate\Auth\Events\Registered',
            'App\Listeners\EventToLogSubscriber@onRegistered'
        );

        $events->listen(
            'App\Events\Auth\UserInvited',
            'App\Listeners\EventToLogSubscriber@onInvited'
        );

        $events->listen(
            'Illuminate\Auth\Events\Verified',
            'App\Listeners\EventToLogSubscriber@onVerified'
        );
    }
}
