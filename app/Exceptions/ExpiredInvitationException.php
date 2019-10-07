<?php

namespace App\Exceptions;

use App\Enums\Logs\EventType;
use App\Enums\Logs\ExceptionType;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ExpiredInvitationException extends HttpException
{
    public function __construct()
    {
        parent::__construct(403, 'Invitation validity ended.');
    }

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

    public function render(): RedirectResponse
    {
        return redirect()->home()->withNotification([
            'title' => __('errore nella richiesta'),
            'message' => __('L\'invito che hai usato non è più valido. Contatta un amministratore per ricevere uno nuovo link.'),
            'status' => 'error',
            'icon' => 'it-close-circle',
        ]);
    }
}
