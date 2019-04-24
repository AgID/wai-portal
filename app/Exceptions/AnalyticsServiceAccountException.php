<?php

namespace App\Exceptions;

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
        logger()->error('Analytics Service account exception: ' . $this->getMessage());
        // TODO: Notify me!!
    }
}
