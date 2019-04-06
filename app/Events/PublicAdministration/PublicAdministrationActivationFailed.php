<?php

namespace App\Events\PublicAdministration;

use App\Events\PublicAdministration\Contracts\PublicAdministrationEvent;
use App\Models\PublicAdministration;

class PublicAdministrationActivationFailed extends PublicAdministrationEvent
{
    protected $message;

    public function __construct(PublicAdministration $publicAdministration, string $message)
    {
        parent::__construct($publicAdministration);
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
