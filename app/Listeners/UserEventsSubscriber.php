<?php

namespace App\Listeners;

use App\Events\User\UserActivated;
use App\Events\User\UserActivationFailed;
use App\Events\User\UserInvited;
use App\Events\User\UserUpdated;
use App\Events\User\UserUpdating;
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
        $user = $event->getUser();
        $invitedBy = $event->getInvitedBy();
        logger()->info('New user invited: ' . $user->getInfo() . ' by ' . $invitedBy->getInfo()); //TODO: notify me!
        //TODO: if the new user is invited as an admin then notify the public administration via PEC
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
     * Handle user updating event.
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
     * Handle user update event.
     *
     * @param UserUpdated $event the event
     */
    public function onUpdated(UserUpdated $event): void
    {
        $user = $event->getUser();
        if ($user->isDirty('email_verified_at') && !empty($user->email_verified_at) && $user->hasAnalyticsServiceAccount()) {
            $user->updateAnalyticsServiceAccountEmail();
        }
        if ($user->isDirty('email')) {
            $user->sendEmailVerificationNotification();
        }
    }

    /**
     * Handle user access to website changed events.
     *
     * @param UserWebsiteAccessChanged $event the event
     */
    public function onWebsiteAccessChanged(UserWebsiteAccessChanged $event): void
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
    public function onWebsiteAccessChangeFailed(UserWebsiteAccessFailed $event): void
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
            'App\Events\User\UserInvited',
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
            'App\Events\User\UserUpdating',
            'App\Listeners\UserEventsSubscriber@onUpdating'
        );

        $events->listen(
            'App\Events\User\UserUpdated',
            'App\Listeners\UserEventsSubscriber@onUpdated'
        );

        $events->listen(
            'App\Events\User\UserWebsiteAccessChanged',
            'App\Listeners\UserEventsSubscriber@onWebsiteAccessChanged'
        );

        $events->listen(
            'App\Events\User\UserWebsiteAccessFailed',
            'App\Listeners\UserEventsSubscriber@onWebsiteAccessChangeFailed'
        );
    }
}
