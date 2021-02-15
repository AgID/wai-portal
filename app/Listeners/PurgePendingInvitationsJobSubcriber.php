<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Events\Jobs\PurgePendingInvitationsCompleted;
use App\Jobs\ProcessUsersIndex;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;

/**
 * Purge pending invitations job related events subscriber.
 */
class PurgePendingInvitationsJobSubcriber implements ShouldQueue
{
    /**
     * Job completed callback.
     *
     * @param PurgePendingInvitationsCompleted $event the event
     */
    public function onCompleted(PurgePendingInvitationsCompleted $event): void
    {
        logger()->notice(
            'Purge pending invitations completed: ' . count($event->getPurged()) . ' invited user/s purged, ' . count($event->getPending()) . ' invited user/s still pending, ' . count($event->getFailed()) . ' invited user/s failed to purge',
            [
                'event' => EventType::PURGE_PENDING_INVITATIONS_COMPLETED,
            ]
        );

        if (!empty($event->getPurged())) {
            dispatch(new ProcessUsersIndex());
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events the dispatcher
     */
    public function subscribe($events): void
    {
        $events->listen(
            'App\Events\Jobs\PurgePendingInvitationsCompleted',
            'App\Listeners\PurgePendingInvitationsJobSubcriber@onCompleted'
        );
    }
}
