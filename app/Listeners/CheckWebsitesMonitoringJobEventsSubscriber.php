<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Events\Jobs\WebsitesMonitoringCheckCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;

/**
 * Monitor websites activity check job related events subscriber.
 */
class CheckWebsitesMonitoringJobEventsSubscriber implements ShouldQueue
{
    /**
     * Job completed callback.
     *
     * @param WebsitesMonitoringCheckCompleted $event the event
     */
    public function onCompleted(WebsitesMonitoringCheckCompleted $event): void
    {
        logger()->notice(
            'Website tracking check completed: ' . count($event->getArchiving()) . ' website/s not tracking ' . count($event->getArchived()) . ' website/s archived, ' . count($event->getFailed()) . ' website/s check failed',
            [
                'event' => EventType::TRACKING_WEBSITES_CHECK_COMPLETED,
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
            'App\Events\Jobs\WebsitesMonitoringCheckCompleted',
            'App\Listeners\CheckWebsitesMonitoringJobEventsSubscriber@onCompleted'
        );
    }
}
