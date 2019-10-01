<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Enums\UserStatus;
use App\Events\User\UserActivated;
use App\Events\User\UserDeleted;
use App\Events\User\UserInvited;
use App\Events\User\UserLogin;
use App\Events\User\UserLogout;
use App\Events\User\UserReactivated;
use App\Events\User\UserRestored;
use App\Events\User\UserSuspended;
use App\Events\User\UserUpdated;
use App\Events\User\UserUpdating;
use App\Events\User\UserWebsiteAccessChanged;
use App\Models\User;
use App\Traits\InteractsWithRedisIndex;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Date;

/**
 * Users related events subscriber.
 */
class UserEventsSubscriber
{
    use InteractsWithRedisIndex;

    /**
     * Handle user registration events.
     *
     * @param Registered $event the event
     */
    public function onRegistered(Registered $event): void
    {
        $this->updateUsersIndex($event->user);

        //NOTE: user isn't connected to any Public Administration yet
        logger()->notice(
            'New user registered: ' . $event->user->uuid,
            [
                'event' => EventType::USER_REGISTERED,
                'user' => $event->user->uuid,
            ]
        );
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

        $this->updateUsersIndex($user);

        $context = [
            'event' => EventType::USER_INVITED,
            'user' => $user->uuid,
        ];
        if (null !== $event->getPublicAdministration()) {
            $context['pa'] = $event->getPublicAdministration()->ipa_code;
        }
        logger()->notice(
            'New user invited: ' . $user->uuid . ' by ' . $invitedBy->uuid,
            $context
        );
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
            'User ' . $event->user->uuid . ' confirmed email address.',
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
        logger()->notice(
            'User ' . $user->uuid . ' activated',
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
     *
     * @throws \App\Exceptions\AnalyticsServiceAccountException if the Analytics Service account doesn't exist
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind analytics service
     */
    public function onUpdated(UserUpdated $event): void
    {
        $user = $event->getUser();

        if ($user->isDirty('email_verified_at') && !empty($user->email_verified_at) && $user->hasAnalyticsServiceAccount()) {
            $user->updateAnalyticsServiceAccountEmail();
        }
        if ($user->isDirty('email')) {
            $user->sendEmailVerificationNotification($user->status->is(UserStatus::INVITED) ? $user->publicAdministrations()->first() : null);

            logger()->notice('User ' . $user->uuid . ' email address changed',
                [
                    'event' => EventType::USER_EMAIL_CHANGED,
                    'user' => $user->uuid,
                ]
            );
        }

        if ($user->isDirty('status')) {
            logger()->notice('User ' . $user->uuid . ' status changed from "' . UserStatus::getDescription($user->getOriginal('status')) . '" to "' . $user->status->description . '"',
                [
                    'event' => EventType::USER_STATUS_CHANGED,
                    'user' => $user->uuid,
                ]
            );
        }

        $this->updateUsersIndex($user);
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
        logger()->notice(
            'Granted "' . $accessType->description . '" access for website ' . $website->info . ' to user ' . $user->uuid,
            [
                'event' => EventType::USER_WEBSITE_ACCESS_CHANGED,
                'user' => $user->uuid,
                'pa' => $website->publicAdministration->ipa_code,
                'website' => $website->id,
            ]
        );
    }

    /**
     * Handle user login events.
     *
     * @param UserLogin $event the event
     */
    public function onLogin(UserLogin $event): void
    {
        $user = $event->getUser();
        $user->last_access_at = Date::now();
        $user->save();
        logger()->info(
            'User ' . $user->uuid . ' logged in.',
            [
                'user' => $user->uuid,
                'event' => EventType::USER_LOGIN,
            ]
        );
    }

    /**
     * Handle user logout events.
     *
     * @param UserLogout $event the event
     */
    public function onLogout(UserLogout $event): void
    {
        $user = $event->getUser();
        logger()->info(
            'User ' . $user->uuid . ' logged out.',
            [
                'user' => $user->uuid,
                'event' => EventType::USER_LOGOUT,
            ]
        );
    }

    /**
     * Handle user suspended events.
     *
     * @param UserSuspended $event the event
     */
    public function onSuspended(UserSuspended $event): void
    {
        $user = $event->getUser();
        logger()->info(
            'User ' . $user->uuid . ' suspended.',
            [
                'user' => $user->uuid,
                'event' => EventType::USER_SUSPENDED,
            ]
        );
    }

    /**
     * Handle user reactivated events.
     *
     * @param UserReactivated $event the event
     */
    public function onReactivated(UserReactivated $event): void
    {
        $user = $event->getUser();
        logger()->info(
            'User ' . $user->uuid . ' reactivated.',
            [
                'user' => $user->uuid,
                'event' => EventType::USER_REACTIVATED,
            ]
        );
    }

    /**
     * Handle user deleted events.
     *
     * @param UserDeleted $event the event
     */
    public function onDeleted(UserDeleted $event): void
    {
        $user = $event->getUser();
        logger()->notice('User ' . $user->uuid . ' deleted.',
            [
                'event' => EventType::USER_DELETED,
                'user' => $user->uuid,
            ]
        );
    }

    /**
     * Handle user restored events.
     *
     * @param UserRestored $event the event
     */
    public function onRestored(UserRestored $event): void
    {
        $user = $event->getUser();
        logger()->notice('User ' . $user->uuid . ' restored.',
            [
                'event' => EventType::USER_RESTORED,
                'user' => $user->uuid,
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

        $events->listen(
            'App\Events\User\UserLogin',
            'App\Listeners\UserEventsSubscriber@onLogin'
        );

        $events->listen(
            'App\Events\User\UserLogout',
            'App\Listeners\UserEventsSubscriber@onLogout'
        );

        $events->listen(
            'App\Events\User\UserSuspended',
            'App\Listeners\UserEventsSubscriber@onSuspended'
        );

        $events->listen(
            'App\Events\User\UserReactivated',
            'App\Listeners\UserEventsSubscriber@onReactivated'
        );

        $events->listen(
            'App\Events\User\UserDeleted',
            'App\Listeners\UserEventsSubscriber@OnDeleted'
        );

        $events->listen(
            'App\Events\User\UserRestored',
            'App\Listeners\UserEventsSubscriber@OnRestored'
        );
    }
}
