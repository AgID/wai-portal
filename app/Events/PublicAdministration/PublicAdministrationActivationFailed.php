<?php

namespace App\Events\PublicAdministration;

use App\Events\PublicAdministration\Contracts\PublicAdministrationEvent;
use App\Models\PublicAdministration;

/**
 * Public Administration activation failed event.
 */
class PublicAdministrationActivationFailed extends PublicAdministrationEvent
{
    /**
     * The activation error message.
     *
     * @var string the message
     */
    protected $message;

    /**
     * Event constructor.
     *
     * @param PublicAdministration $publicAdministration the public administration
     * @param string $message the error message
     */
    public function __construct(PublicAdministration $publicAdministration, string $message)
    {
        parent::__construct($publicAdministration);
        $this->message = $message;
    }

    /**
     * Get the error message.
     *
     * @return string the error message
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
