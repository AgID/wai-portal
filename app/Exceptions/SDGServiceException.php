<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Exception;
use Symfony\Component\Console\Exception\ExceptionInterface as SymfonyConsoleExceptionInterface;

/**
 * Single Digital Gateway Service connection exception.
 */
class SDGServiceException extends Exception implements SymfonyConsoleExceptionInterface
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        logger()->error(
            'SDG Service exception: ' . $this->getMessage(),
            [
                'event' => EventType::EXCEPTION,
                'exception_type' => ExceptionType::SINGLE_DIGITAL_GATEWAY,
            ]
        );
    }
}
