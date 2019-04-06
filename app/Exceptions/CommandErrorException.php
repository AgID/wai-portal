<?php

namespace App\Exceptions;

use Exception;

class CommandErrorException extends Exception
{
    /**
     * Report the exception.
     *
     * @return void
     */
    public function report()
    {
        logger()->error('Analytics Service command error: ' . $this->getMessage());
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

        return redirect()->home()->withMessage(['error' => 'Il comando inviato al servizio di Analytics ha ritornato un errore. Riprovare successivamente.']); //TODO: put message in lang file
    }
}
