<?php

namespace App\Listeners;

use App\Enums\WebsiteAccessType;
use App\Events\User\UserActivated;
use App\Events\User\UserActivationFailed;
use App\Events\User\UserWebsiteAccessChanged;
use App\Events\User\UserWebsiteAccessFailed;

class UserEventsSubscriber
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
     * @param UserActivated $event
     */
    public function onActivated(UserActivated $event): void
    {
        $user = $event->getUser();
        logger()->info('User ' . $user->getInfo() . ' activated');
    }

    public function onActivationFailed(UserActivationFailed $event)
    {
        $user = $event->getUser();
        logger()->error('User ' . $user->getInfo() . ' activation failed');
    }

    /**
     * @param UserWebsiteAccessChanged $event
     */
    public function onSiteAccessChanged(UserWebsiteAccessChanged $event)
    {
        $user = $event->getUser();
        $website = $event->getWebsite();
        $accessType = $event->getAccessType();
        logger()->info('Granted "' . $accessType->description . '" access for website ' . $website->getInfo() . ' to user ' . $user->getInfo());
    }

    /**
     * @param UserWebsiteAccessFailed $event
     */
    public function onSiteAccessChangeFailed(UserWebsiteAccessFailed $event)
    {
        $user = $event->getUser();
        $website = $event->getWebsite();
        $message = $event->getMessage();
        logger()->error('Unable to change access level for website ' . $website->getInfo() . ' to user ' . $user->getInfo() . ': ' . $message);
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
            'App\Listeners\UserEventsSubscriber@onRegistered'
        );

        $events->listen(
            'App\Events\Auth\UserInvited',
            'App\Listeners\UserEventsSubscriber@onInvited'
        );

        $events->listen(
            'Illuminate\Auth\Events\Verified',
            'App\Listeners\UserEventsSubscriber@onVerified'
        );

        $events->listen(
            'App\Events\User\UserActivated',
            'App\Listeners\UserEventsSubscriber@onActivated'
        );

        $events->listen(
            'App\Events\User\UserActivationFailed',
            'App\Listeners\UserEventsSubscriber@onActivationFailed'
        );

        $events->listen(
            'App\Events\User\UserWebsiteAccessChanged',
            'App\Listeners\UserEventsSubscriber@onSiteAccessChanged'
        );

        $events->listen(
            'App\Events\User\UserWebsiteAccessFailed',
            'App\Listeners\UserEventsSubscriber@onSiteAccessChangeFailed'
        );
    }
}
