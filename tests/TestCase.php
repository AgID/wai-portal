<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Log;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use SqliteForeignKeyHotfix;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->hotfixSqlite();
    }

    protected function expectLogMessage(string $logLevel, $args, int $times = 1): void
    {
        Log::shouldReceive($logLevel)
            ->times($times)
            ->withArgs($args);
    }
}
