<?php

namespace App\Listeners;

use App\Events\Website\WebsiteActivated;
use App\Events\Website\WebsiteAdded;
use App\Events\Website\WebsiteArchived;
use App\Events\Website\WebsiteArchiving;
use App\Events\Website\WebsitePurged;
use App\Events\Website\WebsitePurging;
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
     * @param WebsiteAdded $event the event
     */
    public function onAdded(WebsiteAdded $event): void
    {
        $website = $event->getWebsite();

        //TODO: da testare e verificare per attività "Invio mail e PEC"
//        $publicAdministration = $website->publicAdministration;
//        //Notify Website administrators
//        $users = $publicAdministration->getAdministrators();
//        foreach ($users as $user) {
//            logger()->info('User: ' . $user->getInfo());
//            $user->sendWebsiteActivatedNotification($website);
//        }
//
//        //Notify Public Administration
//        $publicAdministration->sendWebsiteActivatedNotification($website);

        logger()->info('Website ' . $website->getInfo() . ' added of type ' . $website->type->description);
    }

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
//        $users = $publicAdministration->getAdministrators();
//        foreach ($users as $user) {
//            $user->sendWebsiteActivatedNotification($website);
//        }

        logger()->info('Website ' . $website->getInfo() . ' activated');
    }

    /**
     * Website archiving event callback.
     *
     * @param WebsiteArchiving $event the event
     */
    public function onArchiving(WebsiteArchiving $event): void
    {
        $website = $event->getWebsite();

        //TODO: da testare e verificare per attività "Invio mail e PEC"
//        //Notify website administrators
//        $users = $website->getAdministrators($website);
//        foreach ($users as $user) {
//            $user->sendWebsiteArchivingNotification($website, $event->getWebsite());
//        }

        logger()->info('Website ' . $website->getInfo() . ' reported as not active and scheduled for archiving');
    }

    /**
     * Website archived event callback.
     *
     * @param WebsiteArchived $event the event
     */
    public function onArchived(WebsiteArchived $event): void
    {
        $website = $event->getWebsite();

        //TODO: da testare e verificare per attività "Invio mail e PEC"
//        //Notify website administrators
//        $users = $website->getAdministrators($website);
//        foreach ($users as $user) {
//            $user->sendWebsiteArchivedNotification($website);
//        }

        logger()->info('Website ' . $website->getInfo() . ' archived due to inactivity');
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
//        $publicAdministration = $website->publicAdministration;
//        //Notify Website administrators
//        $users = $publicAdministration->getAdministrators();
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
        $website = json_decode($event->getWebsiteJson());
        $websiteInfo = '"' . $website->name . '" [' . $website->slug . ']';
        logger()->info('Website ' . $websiteInfo . ' purged');
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events the dispatcher
     */
    public function subscribe($events): void
    {
        $events->listen(
            'App\Events\Website\WebsiteAdded',
            'App\Listeners\WebsiteEventsSubscriber@onAdded'
        );
        $events->listen(
            'App\Events\Website\WebsiteActivated',
            'App\Listeners\WebsiteEventsSubscriber@onActivated'
        );
        $events->listen(
            'App\Events\Website\WebsiteArchiving',
            'App\Listeners\WebsiteEventsSubscriber@onArchiving'
        );
        $events->listen(
            'App\Events\Website\WebsiteArchived',
            'App\Listeners\WebsiteEventsSubscriber@onArchived'
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
