<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Illuminate\Http\Response;

class ExpiredVerificationException extends ExpiredUrlException
{
    public function __construct()
    {
        parent::__construct('Verification link validity ended.');
    }

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

    public function render(): Response
    {
        return $this->buildResponse();
    }
}
