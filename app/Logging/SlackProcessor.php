<?php

namespace App\Logging;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use App\Enums\Logs\JobType;

/**
 * Slack channel data processor.
 */
class SlackProcessor
{
    /**
     * Customize log data.
     *
     * @param array $record the data log record
     *
     * @return array the modified data log record
     */
    public function __invoke(array $record)
    {
        if (isset($record['context']['event'])) {
            $record['context']['event'] = EventType::getKey($record['context']['event']);
        }
        if (isset($record['context']['job'])) {
            $record['context']['job'] = JobType::getKey($record['context']['job']);
        }
        if (isset($record['context']['exception_type'])) {
            $record['context']['exception_type'] = ExceptionType::getKey($record['context']['exception_type']);
        }

        return $record;
    }
}
