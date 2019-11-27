<?php

namespace Tests\Feature;

use App\Enums\Logs\EventType;
use App\Events\Jobs\WebsiteIndexUpdateCompleted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WebsitesIndexEventSubscriberTest extends TestCase
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
                'Website index update completed: ' . count($inserted) . ' website/s updated ' . count($failed) . ' website/s failed',
                [
                    'event' => EventType::WEBSITES_INDEXING_COMPLETED,
                ],
            ]
        );

        event(new WebsiteIndexUpdateCompleted($inserted, $failed));
    }
}
