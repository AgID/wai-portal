<?php

namespace Tests\Unit;

use App\Events\Jobs\ClosedBetaWhitelistUpdateFailed;
use App\Jobs\UpdateClosedBetaWhitelist;
use App\Models\ClosedBetaWhitelist;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

/**
 * Test closed beta whitelist web hook call processing job.
 */
class UpdateClosedBetaWhitelistTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Event::fake();
    }

    /**
     * Test job processing.
     */
    public function testClosedBetaWhitelistUpdateJob(): void
    {
        $data = [
            'ref' => 'develop',
            'repository' => [
                'full_name' => 'pdavide/wai-portal',
            ],
        ];

        Config::set('webhook-client.configs.0.repository.full_name', 'pdavide/wai-portal');
        Config::set('webhook-client.configs.0.repository.branch', 'develop');
        Config::set('webhook-client.configs.0.repository.file_name', 'resources/data/config.yml');

        Cache::shouldReceive('forever')
            ->withSomeOfArgs(UpdateClosedBetaWhitelist::CLOSED_BETA_WHITELIST_KEY)
            ->once()
            ->andReturn(true);

        $webookWhitelist = factory(ClosedBetaWhitelist::class)->make([
            'payload' => $data,
        ]);

        dispatch(new UpdateClosedBetaWhitelist($webookWhitelist));

        Event::assertNotDispatched(ClosedBetaWhitelistUpdateFailed::class);
    }

    /**
     * Test job processing failed.
     */
    public function testClosedBetaWhitelistUpdateJobFailed(): void
    {
        $data = [
            'ref' => 'fake',
            'repository' => [
                'full_name' => 'pdavide/wai-portal',
            ],
        ];

        Config::set('webhook-client.configs.0.repository.file_name', 'fake.yml');

        $webookWhitelist = factory(ClosedBetaWhitelist::class)->make([
            'payload' => $data,
        ]);

        dispatch(new UpdateClosedBetaWhitelist($webookWhitelist));

        Event::assertDispatched(ClosedBetaWhitelistUpdateFailed::class);
    }
}
