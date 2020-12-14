<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Exception;
use Illuminate\Http\RedirectResponse;

/**
 * Operation not allowed exception.
 */
class InvalidKeyException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        logger()->error('Key not found: ' . $this->getMessage(),
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
        return redirect()->home()->withNotification([
            'title' => __('chiave non trovata'),
            'message' => __('La chiave selezionata non può essere trovata.'),
            'status' => 'error',
            'icon' => 'it-close-circle',
        ]);
    }
}
