<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Response;

/**
 * Generic expired link exception.
 */
abstract class ExpiredUrlException extends Exception
{
    /**
     * Report the exception.
     */
    abstract public function report(): void;

    /**
     * Render the exception into an HTTP response.
     *
     * @param bool $invited true if expired link is a user invitation, false otherwise
     *
     * @return \Illuminate\Http\Response the response
     */
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
