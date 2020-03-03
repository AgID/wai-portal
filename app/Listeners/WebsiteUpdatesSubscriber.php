<?php

namespace App\Listeners;

use App\Events\Website\WebsiteStatusChanged;
use App\Events\Website\WebsiteUpdated;
use App\Events\Website\WebsiteUrlChanged;
use Illuminate\Events\Dispatcher;

/**
 * Website model update events subscriber.
 */
class WebsiteUpdatesSubscriber
{
    /**
     * Handle website model updated events.
     *
     * @param WebsiteUpdated $event the event
     */
    public function onUpdated(WebsiteUpdated $event): void
    {
        $website = $event->getWebsite();
        if ($website->isDirty('status')) {
            event(new WebsiteStatusChanged($website, $website->getOriginal('status')));
        }

        if ($website->isDirty('slug')) {
            event(new WebsiteUrlChanged($website, $website->getOriginal('url')));
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
            'App\Events\Website\WebsiteUpdated',
            'App\Listeners\WebsiteUpdatesSubscriber@onUpdated'
        );
    }
}
