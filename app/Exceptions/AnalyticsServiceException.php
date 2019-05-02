<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

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
        logger()->error('Analytics Service exception: ' . $this->getMessage());
        // TODO: Notify me!!
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return RedirectResponse the response
     */
    public function render(): RedirectResponse
    {
        return redirect()->home()->withMessage(['error' => 'Il servizio remoto di Analytics non è disponibile. Riprovare successivamente.']); //TODO: put message in lang file
    }
}
