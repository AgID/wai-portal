<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Exception;
use Illuminate\Http\RedirectResponse;

class InvalidUserStatusException extends Exception
{
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

    public function render(): RedirectResponse
    {
        return redirect()->home()->withMessage(['error' => 'Il comando richiesto non è valido per lo stato attuale dell\'utente']); //TODO: put message in lang file
    }
}
