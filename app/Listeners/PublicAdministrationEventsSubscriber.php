<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Events\PublicAdministration\PublicAdministrationActivated;
use App\Events\PublicAdministration\PublicAdministrationActivationFailed;
use App\Events\PublicAdministration\PublicAdministrationNotFoundInIpa;
use App\Events\PublicAdministration\PublicAdministrationPrimaryWebsiteUpdated;
use App\Events\PublicAdministration\PublicAdministrationPurged;
use App\Events\PublicAdministration\PublicAdministrationRegistered;
use App\Events\PublicAdministration\PublicAdministrationUpdated;
use App\Models\PublicAdministration;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\Facades\Cache;

/**
 * Public Administration related events subscriber.
 */
class PublicAdministrationEventsSubscriber implements ShouldQueue
{
    /**
     * Public Administration registered callback.
     *
     * @param PublicAdministrationRegistered $event the event
     */
    public function onRegistered(PublicAdministrationRegistered $event): void
    {
        Cache::forget(PublicAdministration::PUBLIC_ADMINISTRATION_COUNT_KEY);
        $publicAdministration = $event->getPublicAdministration();
        $user = $event->getUser();

        //Notify registering user
        $user->sendPublicAdministrationRegisteredNotification($publicAdministration);

        if ($publicAdministration->rtd_mail) {
            //Notify RTD
            $publicAdministration->sendPublicAdministrationRegisteredNotificationToRTD();
        }

        logger()->notice(
            'User ' . $user->uuid . ' registered Public Administration ' . $publicAdministration->info,
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_REGISTERED,
                'pa' => $publicAdministration->ipa_code,
                'user' => $user->uuid,
            ]
        );
    }

    /**
     * Public administration activated callback.
     *
     * @param PublicAdministrationActivated $event the event
     */
    public function onActivated(PublicAdministrationActivated $event): void
    {
        $publicAdministration = $event->getPublicAdministration();

        try {
            //NOTE: if RollUp Reporting plugin isn't installed on remote Analytics Service,
            //      a CommandErrorException is expected to be thrown
            $publicAdministration->registerRollUp();
        } catch (Exception $exception) {
            report($exception);
        }

        //Notify user (this user is also the only public administration administrator)
        $user = $publicAdministration->users()->first();
        $user->sendPublicAdministrationActivatedNotification($publicAdministration);

        logger()->notice(
            'Public Administration ' . $publicAdministration->info . ' activated',
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_ACTIVATED,
                'pa' => $publicAdministration->ipa_code,
            ]
        );
    }

    /**
     * Public administration activation failed callback.
     *
     * @param PublicAdministrationActivationFailed $event the event
     */
    public function onActivationFailed(PublicAdministrationActivationFailed $event): void
    {
        $publicAdministration = $event->getPublicAdministration();
        logger()->error(
            'Public Administration ' . $publicAdministration->info . ' activation failed: ' . $event->getMessage(),
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_ACTIVATION_FAILED,
                'pa' => $publicAdministration->ipa_code,
            ]
        );
    }

    /**
     * Public Administration updated callback.
     *
     * @param PublicAdministrationUpdated $event the public administration updated event
     */
    public function onUpdated(PublicAdministrationUpdated $event): void
    {
        $publicAdministration = $event->getPublicAdministration();
        logger()->notice(
            'Public Administration ' . $publicAdministration->info . ' updated',
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_UPDATED,
                'pa' => $publicAdministration->ipa_code,
            ]
        );
    }

    /**
     * Public Administration not found during update from ipa callback.
     *
     * @param PublicAdministrationNotFoundInIpa $event the public administration not found in ipa event
     */
    public function onNotFoundInIpa(PublicAdministrationNotFoundInIpa $event): void
    {
        // TODO: send notification to super-admins
        $publicAdministration = $event->getPublicAdministration();
        logger()->warning(
            'Public Administration ' . $publicAdministration->info . ' not found',
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_UPDATED,
                'pa' => $publicAdministration->ipa_code,
            ]
        );
    }

    /**
     * Public Administration primary site changed callback.
     *
     * @param PublicAdministrationPrimaryWebsiteUpdated $event the public administration primary site updated event
     */
    public function onPrimaryWebsiteUpdated(PublicAdministrationPrimaryWebsiteUpdated $event): void
    {
        //TODO: decidere come gestire i cambiamenti del sito istituzionale su IPA
        $publicAdministration = $event->getPublicAdministration();
        logger()->warning(
            'Public Administration ' . $publicAdministration->info . ' primary website was changed in IPA index [' . $event->getNewURL() . '].',
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_PRIMARY_WEBSITE_CHANGED,
                'pa' => $publicAdministration->ipa_code,
            ]
        );
    }

    /**
     * Public administration purged callback.
     *
     * @param PublicAdministrationPurged $event the event
     */
    public function onPurged(PublicAdministrationPurged $event): void
    {
        Cache::forget(PublicAdministration::PUBLIC_ADMINISTRATION_COUNT_KEY);
        $user = $event->getUser();
        $publicAdministration = json_decode($event->getPublicAdministrationJson());
        $publicAdministrationInfo = '"' . $publicAdministration->name . '" [' . $publicAdministration->ipa_code . ']';

        $user->sendPublicAdministrationPurgedNotification($publicAdministration);

        logger()->notice(
            'Public Administration ' . $publicAdministrationInfo . ' purged',
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_PURGED,
                'pa' => $publicAdministration->ipa_code,
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
            'App\Events\PublicAdministration\PublicAdministrationRegistered',
            'App\Listeners\PublicAdministrationEventsSubscriber@onRegistered'
        );

        $events->listen(
            'App\Events\PublicAdministration\PublicAdministrationActivated',
            'App\Listeners\PublicAdministrationEventsSubscriber@onActivated'
        );

        $events->listen(
            'App\Events\PublicAdministration\PublicAdministrationActivationFailed',
            'App\Listeners\PublicAdministrationEventsSubscriber@onActivationFailed'
        );

        $events->listen(
            'App\Events\PublicAdministration\PublicAdministrationUpdated',
            'App\Listeners\PublicAdministrationEventsSubscriber@onUpdated'
        );

        $events->listen(
            'App\Events\PublicAdministration\PublicAdministrationNotFoundInIpa',
            'App\Listeners\PublicAdministrationEventsSubscriber@onNotFoundInIpa'
        );

        $events->listen(
            'App\Events\PublicAdministration\PublicAdministrationPrimaryWebsiteUpdated',
            'App\Listeners\PublicAdministrationEventsSubscriber@onPrimaryWebsiteUpdated'
        );

        $events->listen(
            'App\Events\PublicAdministration\PublicAdministrationPurged',
            'App\Listeners\PublicAdministrationEventsSubscriber@onPurged'
        );
    }
}
