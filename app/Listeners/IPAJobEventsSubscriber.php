<?php

namespace App\Listeners;

use App\Events\Jobs\IPAUpdateCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;

/**
 * IPA update job events listener.
 */
class IPAJobEventsSubscriber implements ShouldQueue
{
    /**
     * Job completed callback.
     *
     * @param IPAUpdateCompleted $event the job completed event
     */
    public function onCompleted(IPAUpdateCompleted $event): void
    {
        logger()->info('Completed update of Public administrations from IPA list: ' . count($event->getUpdates()) . ' registered Public Administration/s updated');
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events the dispatcher
     */
    public function subscribe($events): void
    {
        $events->listen(
            'App\Events\Jobs\IPAUpdateCompleted',
            'App\Listeners\IPAJobEventsSubscriber@onCompleted'
        );
    }
}
