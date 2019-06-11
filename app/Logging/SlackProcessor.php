<?php

namespace App\Logging;

use App\Enums\Logs\EventType;

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
            $record['context']['job'] = EventType::getKey($record['context']['job']);
        }
        if (isset($record['context']['type'])) {
            $record['context']['type'] = EventType::getKey($record['context']['type']);
        }

        return $record;
    }
}
