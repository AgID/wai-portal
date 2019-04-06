<?php

namespace App\Events\User;

use App\Events\User\Contracts\UserEvent;
use App\Models\User;

class UserActivationFailed extends UserEvent
{
    protected $message;

    public function __construct(User $user, string $message)
    {
        parent::__construct($user);
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
