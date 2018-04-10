<?php

namespace App\Exceptions;

use Exception;

class AnalyticsServiceException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        // TODO: Notify me!!
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\Response
     */
    public function render()
    {
        logger()->error($this->getMessage());
        return redirect()->home()->withMessage(['error' => 'Il servizio remoto di Analytics non è disponibile. Riprovare successivamente.']); //TODO: put message in lang file
    }
}
