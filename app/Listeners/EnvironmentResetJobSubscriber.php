<?php

namespace App\Listeners;

use App\Enums\Logs\EventType;
use App\Events\Jobs\EnvironmentResetCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class EnvironmentResetJobSubscriber implements ShouldQueue
{
    public function onCompleted(EnvironmentResetCompleted $event): void
    {
        logger()->notice(
            'Environment reset completed: ' . count($event->getCompleted()) . ' commands successful, ' . count($event->getFailed()) . ' commands failed',
            [
                'event' => EventType::ENVIRONMENT_RESET_COMPLETED,
            ]
        );
    }

    public function subscribe($events): void
    {
        $events->listen(
            'App\Events\Jobs\EnvironmentResetCompleted',
            'App\Listeners\EnvironmentResetJobSubscriber@onCompleted'
        );
    }
}
