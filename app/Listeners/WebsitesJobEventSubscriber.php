<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Events\Jobs\WebsiteIndexUpdateCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;

/**
 * Websites index job events listener.
 */
class WebsitesJobEventSubscriber implements ShouldQueue
{
    /**
     * Job completed callback.
     *
     * @param WebsiteIndexUpdateCompleted $event the event
     */
    public function onCompleted(WebsiteIndexUpdateCompleted $event): void
    {
        logger()->info(
            'Website index update completed: ' . count($event->getInserted()) . ' website/s updated ' . count($event->getFailed()) . ' website/s failed',
            [
                'event' => EventType::WEBSITES_INDEXING_COMPLETED,
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
            'App\Events\Jobs\WebsiteIndexUpdateCompleted',
            'App\Listeners\WebsitesJobEventSubscriber@onCompleted'
        );
    }
}
