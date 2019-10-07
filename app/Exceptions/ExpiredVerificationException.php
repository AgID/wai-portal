<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Illuminate\Http\Response;

/**
 * Expired user verification exception.
 */
class ExpiredVerificationException extends ExpiredUrlException
{
    /**
     * Create a new exception instance.
     */
    public function __construct()
    {
        parent::__construct('Verification link validity ended.');
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        logger()->error(
            $this->getMessage(),
            [
                'event' => EventType::EXCEPTION,
                'exception_type' => ExceptionType::EXPIRED_VERIFICATION_LINK_USAGE,
                'exception' => $this,
            ]
        );
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return Response the response
     */
    public function render(): Response
    {
        return $this->buildResponse();
    }
}
