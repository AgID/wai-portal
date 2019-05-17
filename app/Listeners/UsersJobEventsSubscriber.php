<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Events\Jobs\UserIndexUpdateCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class UsersJobEventsSubscriber implements ShouldQueue
{
    public function onCompleted(UserIndexUpdateCompleted $event): void
    {
        logger()->info(
            'User index update completed: ' . count($event->getInserted()) . ' user/s updated ' . count($event->getFailed()) . ' user/s failed',
            [
                'event' => EventType::USERS_INDEXING_COMPLETED,
            ]
        );
    }

    public function subscribe($events): void
    {
        $events->listen(
            'App\Events\Jobs\UserIndexUpdateCompleted',
            'App\Listeners\UsersJobEventsSubscriber@onCompleted'
        );
    }
}
