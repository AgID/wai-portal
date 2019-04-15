<?php

namespace App\Listeners;

use App\Events\Jobs\PendingWebsitesCheckCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;

/**
 * Pending websites check job related events subscriber.
 */
class CheckPendingWebsiteJobsEventsSubscriber implements ShouldQueue
{
    /**
     * Job completed callback.
     *
     * @param PendingWebsitesCheckCompleted $event the event
     */
    public function onCompleted(PendingWebsitesCheckCompleted $event): void
    {
        logger()->info('Pending website check completed: ' . count($event->getActivated()) . ' website/s activated, ' . count($event->getPurging()) . ' website/s scheduled for purging, ' . count($event->getPurged()) . ' website/s purged');
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events the dispatcher
     */
    public function subscribe($events): void
    {
        $events->listen(
            'App\Events\Jobs\PendingWebsitesCheckCompleted',
            'App\Listeners\CheckPendingWebsiteJobsEventsSubscriber@onCompleted'
        );
    }
}
