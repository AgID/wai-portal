<?php

namespace Tests\Feature;

use App\Enums\Logs\EventType;
use App\Events\Jobs\ClosedBetaWhitelistUpdateFailed;
use Tests\TestCase;

/**
 * Test closed beta update job events listener.
 */
class UpdateClosedBetaWhitelistJobSubscriberTest extends TestCase
{
    /**
     * Test update failed events handler.
     */
    public function testOnFailed(): void
    {
        $this->expectLogMessage('error', [
            'Closed beta whitelist update failed with code 400 and message Fake message',
            [
                'event' => EventType::CLOSED_BETA_WHITELIST_UPDATE_FAILED,
            ],
        ]);

        event(new ClosedBetaWhitelistUpdateFailed(400, 'Fake message'));
    }
}
