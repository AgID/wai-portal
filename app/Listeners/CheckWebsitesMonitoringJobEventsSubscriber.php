<?php

namespace App\Listeners;

use App\Events\Jobs\WebsitesMonitoringCheckCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckWebsitesMonitoringJobEventsSubscriber implements ShouldQueue
{
    /**
     * @param WebsitesMonitoringCheckCompleted $event
     */
    public function onCompleted(WebsitesMonitoringCheckCompleted $event): void
    {
        logger()->info('Website tracking check completed: ' . count($event->getArchiving()) . ' website/s not tracking ' . count($event->getArchived()) . ' website/s archived, ' . count($event->getFailed()) . ' website/s check failed');
    }

    /**
     * @param $events
     */
    public function subscribe($events): void
    {
        $events->listen(
            'App\Events\Jobs\WebsitesMonitoringCheckCompleted',
            'App\Listeners\CheckWebsitesMonitoringJobEventsSubscriber@onCompleted'
        );
    }
}
