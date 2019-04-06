<?php

namespace App\Listeners;

use App\Events\Jobs\PendingWebsitesCheckCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckPendingWebsiteJobsEventsSubscriber implements ShouldQueue
{
    public function onCompleted(PendingWebsitesCheckCompleted $event)
    {
        logger()->info('Pending website check completed: ' . count($event->getActivated()) . ' website/s activated, ' . count($event->getPurging()) . ' website/s scheduled for purging, ' . count($event->getPurged()) . ' website/s purged');
    }

    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Jobs\PendingWebsitesCheckCompleted',
            'App\Listeners\CheckPendingWebsiteJobsEventsSubscriber@onCompleted'
        );
    }
}
