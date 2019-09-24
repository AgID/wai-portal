<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Events\Jobs\PublicAdministrationsUpdateFromIpaCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;

/**
 * IPA update job events listener.
 */
class UpdatePublicAdministrationsFromIpaJobEventsSubscriber implements ShouldQueue
{
    /**
     * Job completed callback.
     *
     * @param PublicAdministrationsUpdateFromIpaCompleted $event the job completed event
     */
    public function onCompleted(PublicAdministrationsUpdateFromIpaCompleted $event): void
    {
        logger()->notice(
            'Completed update of Public administrations from IPA index: ' . count($event->getUpdates()) . ' registered Public Administration/s updated',
            [
                'event' => EventType::UPDATE_PA_FROM_IPA_COMPLETED,
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
            'App\Events\Jobs\PublicAdministrationsUpdateFromIpaCompleted',
            'App\Listeners\UpdatePublicAdministrationsFromIpaJobEventsSubscriber@onCompleted'
        );
    }
}
