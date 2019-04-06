<?php

namespace App\Listeners;

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
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events the dispatcher
     */
    public function subscribe($events): void
    {
        $events->listen(
            'App\Events\PublicAdministration\PublicAdministrationUpdated',
            'App\Listeners\PublicAdministrationEventsSubscriber@onUpdated'
        );

        $events->listen(
            'App\Events\PublicAdministration\PublicAdministrationWebsiteUpdated',
            'App\Listeners\PublicAdministrationEventsSubscriber@onUpdated'
        );
    }
}
