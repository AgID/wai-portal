<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Events\User\UserActivated;
use App\Events\User\UserInvited;
use App\Events\User\UserUpdated;
use App\Events\User\UserUpdating;
use App\Events\User\UserWebsiteAccessChanged;
use App\Jobs\ProcessUsersList;
use App\Models\User;
use Ehann\RediSearch\Exceptions\FieldNotInSchemaException;
use Ehann\RediSearch\Index;
use Ehann\RedisRaw\PredisAdapter;
use Exception;
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
        $this->updateUsersIndex($event->user);

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

        $this->updateUsersIndex($user);

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
            $user->sendEmailVerificationNotification();
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
        logger()->info(
            'Granted "' . $accessType->description . '" access for website ' . $website->getInfo() . ' to user ' . $user->getInfo(),
            [
                'event' => EventType::USER_WEBSITE_ACCESS_CHANGED,
                'user' => $user->uuid,
                'pa' => $website->publicAdministration->ipa_code,
                'website' => $website->id,
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

    /**
     * Update users index.
     *
     * @param User $user the user to update
     */
    private function updateUsersIndex(User $user): void
    {
        $userIndex = new Index(
            (new PredisAdapter())->connect(config('database.redis.indexes.host'), config('database.redis.indexes.port'), config('database.redis.indexes.database')),
            ProcessUsersList::USER_INDEX_NAME
        );

        try {
            $userIndex->addTagField('pas')
                ->addTextField('uuid')
                ->addTextField('familyName', 2.0, true)
                ->addTextField('name', 2.0, true)
                ->create();
        } catch (Exception $e) {
            // Index already exists, it's ok!
        }

        try {
            $userDocument = $userIndex->makeDocument($user->uuid);
            $userDocument->uuid->setValue($user->uuid);
            $userDocument->name->setValue($user->name);
            $userDocument->familyName->setValue($user->familyName);
            $userDocument->pas->setValue(implode(',', $user->publicAdministrations()->get()->pluck('ipa_code')->toArray()));
            $userIndex->replace($userDocument);
        } catch (FieldNotInSchemaException $exception) {
            report($exception);
        }
    }
}
