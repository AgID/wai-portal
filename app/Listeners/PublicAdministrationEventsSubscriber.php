<?php

namespace App\Listeners;

use App\Events\PublicAdministration\PublicAdministrationActivated;
use App\Events\PublicAdministration\PublicAdministrationActivationFailed;
use App\Events\PublicAdministration\PublicAdministrationPurged;
use App\Events\PublicAdministration\PublicAdministrationUpdated;
use App\Events\PublicAdministration\PublicAdministrationWebsiteUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;

/**
 * Public Administration related events subscriber.
 */
class PublicAdministrationEventsSubscriber implements ShouldQueue
{
    /**
     * Public administration activated callback.
     *
     * @param PublicAdministrationActivated $event the event
     */
    public function onActivated(PublicAdministrationActivated $event): void
    {
        $publicAdministration = $event->getPublicAdministration();
        logger()->info('Public Administration ' . $publicAdministration->getInfo() . ' activated');
    }

    /**
     * Public administration activation failed callback.
     *
     * @param PublicAdministrationActivationFailed $event the event
     */
    public function onActivationFailed(PublicAdministrationActivationFailed $event): void
    {
        $publicAdministration = $event->getPublicAdministration();
        logger()->error('Public Administration ' . $publicAdministration->getInfo() . ' activation failed: ' . $event->getMessage());
    }

    /**
     * Public Administration updated callback.
     *
     * @param PublicAdministrationUpdated $event the public administration updated event
     */
    public function onUpdated(PublicAdministrationUpdated $event): void
    {
        $publicAdministration = $event->getPublicAdministration();
        logger()->info('Public Administration ' . $publicAdministration->getInfo() . ' updated');
    }

    /**
     * Public Administration primary site changed callback.
     *
     * @param PublicAdministrationWebsiteUpdated $event the public administration primary site updated event
     */
    public function onPrimaryWebsiteUpdated(PublicAdministrationWebsiteUpdated $event): void
    {
        //TODO: decidere come gestire i cambiamenti del sito istituzionale su IPA
        $publicAdministration = $event->getPublicAdministration();
        logger()->warning('Public Administration ' . $publicAdministration->getInfo() . ' primary website was changed in IPA list [' . $event->getNewURL() . '].');
    }

    /**
     * Public administration purged callback.
     *
     * @param PublicAdministrationPurged $event the event
     */
    public function onPurged(PublicAdministrationPurged $event): void
    {
        logger()->info('Public Administration ' . $event->getPublicAdministration() . ' purged');
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events the dispatcher
     */
    public function subscribe($events): void
    {
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
            'App\Events\PublicAdministration\PublicAdministrationWebsiteUpdated',
            'App\Listeners\PublicAdministrationEventsSubscriber@onUpdated'
        );
        $events->listen(
            'App\Events\PublicAdministration\PublicAdministrationPurged',
            'App\Listeners\PublicAdministrationEventsSubscriber@onPurged'
        );
    }
}
