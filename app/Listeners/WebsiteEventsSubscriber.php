<?php

namespace App\Listeners;

use App\Events\Website\WebsiteActivated;
use App\Events\Website\WebsitePurged;
use App\Events\Website\WebsitePurging;
use Illuminate\Contracts\Queue\ShouldQueue;

class WebsiteEventsSubscriber implements ShouldQueue
{
    public function onActivated(WebsiteActivated $event)
    {
        $website = $event->getWebsite();
        logger()->info('Website ' . $website->getInfo() . ' activated');
    }

    public function onPurging(WebsitePurging $event)
    {
        $website = $event->getWebsite();
        logger()->info('Website ' . $website->getInfo() . ' scheduled purging');
    }

    public function onPurged(WebsitePurged $event)
    {
        logger()->info('Website ' . $event->getWebsite() . ' purged');
    }

    public function subscribe($events): void
    {
        $events->listen(
            'App\Events\Website\WebsiteActivated',
            'App\Listeners\WebsiteEventsSubscriber@onActivated'
        );
        $events->listen(
            'App\Events\Website\WebsitePurging',
            'App\Listeners\WebsiteEventsSubscriber@onPurging'
        );
        $events->listen(
            'App\Events\Website\WebsitePurged',
            'App\Listeners\WebsiteEventsSubscriber@onPurged'
        );
    }
}
