<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Events\Jobs\ClosedBetaWhitelistUpdateFailed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;

/**
 * Closed beta whitelist update events listener.
 */
class UpdateClosedBetaWhitelistJobSubscriber implements ShouldQueue
{
    /**
     * Closed beta whitelist update failed.
     *
     * @param ClosedBetaWhitelistUpdateFailed $event the event
     */
    public function onFailed(ClosedBetaWhitelistUpdateFailed $event): void
    {
        logger()->error(
            'Closed beta whitelist update failed with code ' . $event->getCode() . ' and message ' . $event->getPhrase(),
            [
                'event' => EventType::CLOSED_BETA_WHITELIST_UPDATE_FAILED,
            ],
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
            'App\Events\Jobs\ClosedBetaWhitelistUpdateFailed',
            'App\Listeners\UpdateClosedBetaWhitelistJobSubscriber@onFailed'
        );
    }
}
