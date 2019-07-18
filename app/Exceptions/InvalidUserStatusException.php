<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Exception;
use Illuminate\Http\RedirectResponse;

/**
 * Invalid user status for operation exception.
 */
class InvalidUserStatusException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        logger()->error('Operation not allowed in current user status: ' . $this->getMessage(),
            [
                'event' => EventType::EXCEPTION,
                'exception_type' => ExceptionType::INVALID_USER_STATUS,
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
        return redirect()->home()->withMessage(['error' => "Il comando richiesto non è valido per lo stato attuale dell'utente"]); //TODO: put message in lang file
    }
}
