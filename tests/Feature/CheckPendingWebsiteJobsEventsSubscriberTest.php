<?php

namespace Tests\Feature;

use App\Enums\Logs\EventType;
use App\Events\Jobs\PendingWebsitesCheckCompleted;
use Tests\TestCase;

class CheckPendingWebsiteJobsEventsSubscriberTest extends TestCase
{
    public function testCheckPendingWebsiteJobsCompleted(): void
    {
        $activated = [
            [
                'website' => 'fake-activated',
            ],
        ];
        $purged = [
            [
                'website' => 'fake-purged',
            ],
        ];
        $purging = [
            [
                'website' => 'fake-purging',
            ],
        ];
        $failed = [
            [
                'website' => 'fake-failed',
            ],
        ];

        $this->expectLogMessage('notice', [
            'Pending website check completed: ' . count($activated) . ' website/s activated, ' . count($purging) . ' website/s scheduled for purging, ' . count($purged) . ' website/s purged',
            ['event' => EventType::PENDING_WEBSITES_CHECK_COMPLETED],
        ]);

        event(new PendingWebsitesCheckCompleted($activated, $purging, $purged, $failed));
    }
}
