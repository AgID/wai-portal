<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Expired invitation exception.
 */
class ExpiredInvitationException extends HttpException
{
    /**
     * Create a new exception instance.
     */
    public function __construct()
    {
        parent::__construct(403, 'Invitation validity ended.');
    }

    /**
     * Report the exception.
     */
    public function report(): void
    {
        logger()->error(
            $this->getMessage(),
            [
                'event' => EventType::EXCEPTION,
                'exception_type' => ExceptionType::EXPIRED_INVITATION_LINK_USAGE,
                'exception' => $this,
            ],
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
            'title' => __('errore nella richiesta'),
            'message' => __("L'invito che hai usato non è più valido, contatta un amministratore per riceverne uno."),
            'status' => 'error',
            'icon' => 'it-close-circle',
        ]);
    }
}
