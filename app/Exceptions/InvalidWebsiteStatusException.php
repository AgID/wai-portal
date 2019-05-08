<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;

class InvalidWebsiteStatusException extends Exception
{
    public function report(): void
    {
        logger()->error('Operation not allowed in current website status: ' . $this->getMessage());
    }

    public function render(): RedirectResponse
    {
        return redirect()->home()->withMessage(['error' => 'Il comando richiesto non è valido per lo stato attuale del sito']); //TODO: put message in lang file
    }
}
