<?php

namespace Tests\Unit;

use App\Jobs\UpdateClosedBetaWhitelist;
use App\Models\ClosedBetaWhitelist;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class UpdateClosedBetaWhitelistTest extends TestCase
{
    public function testClosedBetaWhitelistUpdateJob(): void
    {
        $data = ['first item', 'second item'];
        $payload = Yaml::dump($data);

        Cache::shouldReceive('forever')
            ->withArgs(function (...$args) use ($data) {
                return 2 === count($args)
                    && UpdateClosedBetaWhitelist::CLOSED_BETA_WHITELIST_KEY === $args[0]
                    && $data == $args[1]->toArray();
            })
            ->once()
            ->andReturn(true);

        Storage::shouldReceive('put')
            ->withArgs([
                UpdateClosedBetaWhitelist::CLOSED_BETA_WHITELIST_FILENAME,
                $payload,
            ])
            ->once()
            ->andReturn(true);

        $webookWhitelist = factory(ClosedBetaWhitelist::class)->make([
            'payload' => $data,
        ]);

        dispatch(new UpdateClosedBetaWhitelist($webookWhitelist));
    }
}
