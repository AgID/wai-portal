<?php

namespace App\Listeners;

use App\Events\Jobs\IPAUpdateCompleted;
use App\Events\Jobs\IPAUpdateFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;

/**
 * IPA update job events listener.
 */
class IPAJobEventsSubscriber implements ShouldQueue
{
    /**
     * Job failed callback.
     *
     * @param IPAUpdateFailed $event the job failed event
     */
    public function onFailed(IPAUpdateFailed $event): void
    {
        logger()->error('IPA update failed. Reason: ' . $event->getMessage());
    }

    /**
     * Job completed callback.
     *
     * @param IPAUpdateCompleted $event the job completed event
     */
    public function onCompleted(IPAUpdateCompleted $event): void
    {
        logger()->info('Completed import of IPA list from http://www.indicepa.gov.it and ' . count($event->getUpdates()) . ' registered Public Administration/s updated');
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

        $events->listen(
            'App\Events\Jobs\IPAUpdateFailed',
            'App\Listeners\IPAJobEventsSubscriber@onFailed'
        );
    }
}
