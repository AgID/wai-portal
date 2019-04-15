<?php

namespace App\Listeners;

use App\Events\Website\WebsiteActivated;
use App\Events\Website\WebsitePurged;
use App\Events\Website\WebsitePurging;
use App\Models\Website;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Events\Dispatcher;

/**
 * Websites related events subscriber.
 */
class WebsiteEventsSubscriber implements ShouldQueue
{
    /**
     * Website activated event callback.
     *
     * @param WebsiteActivated $event the event
     */
    public function onActivated(WebsiteActivated $event): void
    {
        $website = $event->getWebsite();

        //TODO: da testare e verificare per attività "Invio mail e PEC"
//        $publicAdministration = $website->publicAdministration;
//        //Notify Website administrators
//        $users = $this->getAdministrators($website);
//        foreach ($users as $user) {
//            $user->sendWebsiteActivatedNotification($website);
//        }
//
//        //Notify Public Administration
//        $publicAdministration->sendWebsiteActivatedNotification($website);

        logger()->info('Website ' . $website->getInfo() . ' activated');
    }

    /**
     * Website near-to-be-purged event callback.
     *
     * @param WebsitePurging $event the event
     */
    public function onPurging(WebsitePurging $event): void
    {
        $website = $event->getWebsite();

        //TODO: da testare e verificare per attività "Invio mail e PEC"
//        //Notify Website administrators
//        $users = $this->getAdministrators($website);
//        foreach ($users as $user) {
//            $user->sendWebsitePurgingNotification($website);
//        }

        logger()->info('Website ' . $website->getInfo() . ' scheduled purging');
    }

    /**
     * Website purged event callback.
     *
     * @param WebsitePurged $event the event
     */
    public function onPurged(WebsitePurged $event): void
    {
        logger()->info('Website ' . $event->getWebsite() . ' purged');
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events the dispatcher
     */
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
