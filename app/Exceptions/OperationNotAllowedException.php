<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Exception;
use Illuminate\Http\RedirectResponse;

/**
 * Operation not allowed exception.
 */
class OperationNotAllowedException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        logger()->error('Operation not allowed: ' . $this->getMessage(),
            [
                'event' => EventType::EXCEPTION,
                'exception_type' => ExceptionType::INVALID_OPERATION,
                'exception' => $this,
            ]
        );
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return RedirectResponse the response
     */
    public function render(): RedirectResponse
    {
        return redirect()->home()->withAlert(['error' => 'Il comando richiesto non è consentito']); //TODO: put message in lang file
    }
}
