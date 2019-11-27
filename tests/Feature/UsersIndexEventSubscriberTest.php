<?php

namespace Tests\Feature;

use App\Enums\Logs\EventType;
use App\Events\Jobs\UserIndexUpdateCompleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UsersIndexEventSubscriberTest extends TestCase
{
    use RefreshDatabase;

    public function testUpdateCompleted(): void
    {
        $inserted = [
            'fakeinserted' => [],
        ];

        $failed = [
            'fakefailed' => [],
        ];

        $this->expectLogMessage(
            'info',
            [
                'User index update completed: ' . count($inserted) . ' user/s updated ' . count($failed) . ' user/s failed',
                [
                    'event' => EventType::USERS_INDEXING_COMPLETED,
                ],
            ]
        );

        event(new UserIndexUpdateCompleted($inserted, $failed));
    }
}
