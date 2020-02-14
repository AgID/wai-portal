<?php

namespace Tests\Feature;

use App\Enums\Logs\EventType;
use App\Events\Jobs\ClosedBetaWhitelistUpdateFailed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateClosedBetaWhitelistJobSubscriberTest extends TestCase
{
    use RefreshDatabase;

    public function testOnFailed(): void
    {
        $this->expectLogMessage('error', [
            'Closed beta whitelist update failed with code 400 and message Fake message',
            [
                'event' => EventType::CLOSED_BETA_WHITELIST_UPDATE_FAILED,
            ]
        ]);

        event(new ClosedBetaWhitelistUpdateFailed(400, 'Fake message'));
    }
}
