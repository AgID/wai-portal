<?php

namespace Tests\Unit;

use App\Adapter\RedisSentinelPredisAdapter;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

class RedisSentinelPredisAdapterTest extends TestCase
{
    public function testIndexSentinelAdapter(): void
    {
        $this->assertEquals(Redis::connection('websites-sentinel')->client(), (new RedisSentinelPredisAdapter('websites'))->connect()->redis);
    }
}
