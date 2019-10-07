<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

abstract class ExpiredUrlException extends Exception
{
    abstract public function report(): void;

    protected function buildResponse(bool $invited = false): Response
    {
        return response()->view(
            'auth.url_expired',
            [
                'invitation' => $invited,
            ]
        );
    }
}
