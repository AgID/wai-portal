<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Exception;
use Illuminate\Http\RedirectResponse;

class TenantIdNotSetException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        logger()->error(
            'Tenant id is not set in the user session: ' . $this->getMessage(),
            [
                'event' => EventType::EXCEPTION,
                'type' => ExceptionType::TENANT_SELECTION,
                'exception' => $this,
            ]
        );
        // TODO: Notify me!!
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return RedirectResponse the response
     */
    public function render(): RedirectResponse
    {
        return redirect()->home()->withMessage(['error' => "Qualcosa non ha funzionato nella gestione della sessione. Prova ad eseguire nuovamente l'accesso."]); //TODO: put message in lang file
    }
}
