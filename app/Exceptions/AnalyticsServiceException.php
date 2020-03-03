<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Exception;
use Illuminate\Http\RedirectResponse;

/**
 * Analytics Service connection exception.
 */
class AnalyticsServiceException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        logger()->critical(
            'Analytics Service exception: ' . $this->getMessage(),
            [
                'event' => EventType::EXCEPTION,
                'exception_type' => ExceptionType::ANALYTICS_SERVICE,
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
            'title' => __('errore del server'),
            'message' => implode("\n", [
                __('Si è verificato un errore relativamente alla tua richiesta.'),
                __('Puoi riprovare più tardi o :contact_support.', ['contact_support' => '<a href="' . route('contacts') . '">' . __('contattare il supporto tecnico') . '</a>']),
            ]),
            'status' => 'error',
            'icon' => 'it-close-circle',
        ]);
    }
}
