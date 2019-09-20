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
        return redirect()->home()->withNotification([
            'title' => __('errore nella richiesta'),
            'message' => __("La richiesta non è valida per lo stato attuale dell'utente."),
            'status' => 'error',
            'icon' => 'it-close-circle',
        ]);
    }
}
