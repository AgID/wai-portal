<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;

class OperationNotAllowedException extends Exception
{
    public function report(): void
    {
        logger()->error('Operation not allowed: ' . $this->getMessage());
    }

    public function render(): RedirectResponse
    {
        return redirect()->home()->withMessage(['error' => 'Il comando richiesto non è consentito']); //TODO: put message in lang file
    }
}
