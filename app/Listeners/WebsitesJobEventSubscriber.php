<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Events\Jobs\WebsiteIndexUpdateCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class WebsitesJobEventSubscriber implements ShouldQueue
{
    public function onCompleted(WebsiteIndexUpdateCompleted $event): void
    {
        logger()->info(
            'Website index update completed: ' . count($event->getInserted()) . ' website/s updated ' . count($event->getFailed()) . ' website/s failed',
            [
                'event' => EventType::WEBSITES_INDEXING_COMPLETED,
            ]
        );
    }

    public function subscribe($events): void
    {
        $events->listen(
            'App\Events\Jobs\WebsiteIndexUpdateCompleted',
            'App\Listeners\WebsitesJobEventSubscriber@onCompleted'
        );
    }
}
