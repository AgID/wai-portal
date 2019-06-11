<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Exception;

/**
 * Analytics Service account exception.
 */
class AnalyticsServiceAccountException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        logger()->critical(
            'Analytics Service account exception: ' . $this->getMessage(),
            [
                'event' => EventType::EXCEPTION,
                'type' => ExceptionType::ANALYTICS_ACCOUNT,
                'exception' => $this,
            ]
        );
    }
}
