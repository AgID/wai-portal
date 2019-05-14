<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Events\User\UserActivated;
use App\Events\User\UserInvited;
use App\Events\User\UserUpdated;
use App\Events\User\UserUpdating;
use App\Events\User\UserWebsiteAccessChanged;
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
        //NOTE: user isn't connected to any P.A. yet
        logger()->info(
            'New user registered: ' . $event->user->getInfo(),
            [
                'event' => EventType::USER_REGISTERED,
                'user' => $event->user->uuid,
            ]
        ); //TODO: notify me!
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

        $context = [
            'event' => EventType::USER_INVITED,
            'user' => $user->uuid,
        ];
        if (null !== $event->getPublicAdministration()) {
            $context['pa'] = $event->getPublicAdministration()->ipa_code;
        }
        logger()->info(
            'New user invited: ' . $user->getInfo() . ' by ' . $invitedBy->getInfo(),
            $context
        ); //TODO: notify me!
        //TODO: if the new user is invited as an admin then notify the public administration via PEC
    }

    /**
     * Handle user verification events.
     *
     * @param Verified $event the event
     */
    public function onVerified(Verified $event): void
    {
        logger()->info(
            'User ' . $event->user->getInfo() . ' confirmed email address.',
            [
                'event' => EventType::USER_VERIFIED,
                'user' => $event->user->uuid,
            ]
        ); //TODO: notify me!
    }

    /**
     * Handle user activated events.
     *
     * @param UserActivated $event the event
     */
    public function onActivated(UserActivated $event): void
    {
        $user = $event->getUser();
        logger()->info(
            'User ' . $user->getInfo() . ' activated',
            [
                'event' => EventType::USER_ACTIVATED,
                'user' => $user->uuid,
                'pa' => $event->getPublicAdministration()->ipa_code,
            ]
        );
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
        logger()->info(
            'Granted "' . $accessType->description . '" access for website ' . $website->getInfo() . ' to user ' . $user->getInfo(),
            [
                'event' => EventType::USER_WEBSITE_ACCESS_CHANGED,
                'user' => $user->uuid,
                'pa' => $website->publicAdministration->ipa_code,
            ]
        );
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
    }
}
