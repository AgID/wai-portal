<?php

namespace App\Listeners;

use App\Events\Auth\UserInvited;
use App\Events\User\UserActivated;
use App\Events\User\UserActivationFailed;
use App\Events\User\UserWebsiteAccessChanged;
use App\Events\User\UserWebsiteAccessFailed;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Events\Dispatcher;

/**
 * Users related events subscriber.
 */
class UserEventsSubscriber
{
    /**
     * Handle user registration events.
     *
     * @param Registered $event the event
     */
    public function onRegistered(Registered $event): void
    {
        logger()->info('New user registered: ' . $event->user->getInfo()); //TODO: notify me!
    }

    /**
     * Handle user invitation events.
     *
     * @param UserInvited $event the event
     */
    public function onInvited(UserInvited $event): void
    {
        logger()->info('New user invited: ' . $event->user->getInfo() . ' by ' . $event->invitedBy->getInfo()); //TODO: notify me!
    }

    /**
     * Handle user verification events.
     *
     * @param Verified $event the event
     */
    public function onVerified(Verified $event): void
    {
        logger()->info('User ' . $event->user->getInfo() . ' confirmed email address.'); //TODO: notify me!
    }

    /**
     * Handle user activated events.
     *
     * @param UserActivated $event the event
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
     * Handle user access to website changed events.
     *
     * @param UserWebsiteAccessChanged $event the event
     */
    public function onSiteAccessChanged(UserWebsiteAccessChanged $event): void
    {
        $user = $event->getUser();
        $website = $event->getWebsite();
        $accessType = $event->getAccessType();
        logger()->info('Granted "' . $accessType->description . '" access for website ' . $website->getInfo() . ' to user ' . $user->getInfo());
    }

    /**
     * Handle change user access to website failed.
     *
     * @param UserWebsiteAccessFailed $event the event
     */
    public function onSiteAccessChangeFailed(UserWebsiteAccessFailed $event): void
    {
        $user = $event->getUser();
        $website = $event->getWebsite();
        $message = $event->getMessage();
        logger()->error('Unable to change access level for website ' . $website->getInfo() . ' to user ' . $user->getInfo() . ': ' . $message);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events the dispatcher
     */
    public function subscribe($events): void
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
