<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Events\Jobs\PurgeOrphanedEntitiesCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;

/**
 * Monitor websites activity check job related events subscriber.
 */
class PurgeOrphanedEntitiesJobEventsSubscriber implements ShouldQueue
{
    /**
     * Job completed callback.
     *
     * @param PurgeOrphanedEntitiesCompleted $event the event
     */
    public function onCompleted(PurgeOrphanedEntitiesCompleted $event): void
    {
        logger()->notice(
            'Purge orphaned entities: ' . json_encode($event->getProcessed()),
            [
                'event' => EventType::PURGE_ORPHANED_ENTITIES_COMPLETED,
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
            'App\Events\Jobs\PurgeOrphanedEntitiesCompleted',
            'App\Listeners\PurgeOrphanedEntitiesJobEventsSubscriber@onCompleted'
        );
    }
}
