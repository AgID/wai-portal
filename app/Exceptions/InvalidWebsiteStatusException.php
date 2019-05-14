<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Exception;
use Illuminate\Http\RedirectResponse;

/**
 * Invalid website status for operation exception.
 */
class InvalidWebsiteStatusException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        logger()->error('Operation not allowed in current website status: ' . $this->getMessage(),
            [
                'event' => EventType::EXCEPTION,
                'type' => ExceptionType::INVALID_WEBSITE_STATUS,
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
        return redirect()->home()->withMessage(['error' => 'Il comando richiesto non è valido per lo stato attuale del sito']); //TODO: put message in lang file
    }
}
