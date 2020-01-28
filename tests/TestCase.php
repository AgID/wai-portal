<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Log;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function expectLogMessage(string $logLevel, $args, int $times = 1): void
    {
        Log::shouldReceive($logLevel)
            ->times($times)
            ->withArgs($args);
    }
}
