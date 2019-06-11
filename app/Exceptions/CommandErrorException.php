<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Exception;
use Illuminate\Http\RedirectResponse;

/**
 * Analytics Service command error exception.
 */
class CommandErrorException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        logger()->critical(
            'Analytics Service command error: ' . $this->getMessage(),
            [
                'event' => EventType::EXCEPTION,
                'type' => ExceptionType::ANALYTICS_COMMAND,
                'exception' => $this,
            ]
        );
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\RedirectResponse the response
     */
    public function render(): RedirectResponse
    {
        return redirect()->home()->withMessage(['error' => 'Il comando inviato al servizio di Analytics ha ritornato un errore. Riprovare successivamente.']); //TODO: put message in lang file
    }
}
