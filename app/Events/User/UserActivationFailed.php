<?php

namespace App\Events\User;

use App\Events\User\Contracts\UserEvent;
use App\Models\User;

/**
 * User activation failed event.
 */
class UserActivationFailed extends UserEvent
{
    /**
     * The error message.
     *
     * @var string the message
     */
    protected $message;

    /**
     * Event constructor.
     *
     * @param User $user the user
     * @param string $message the error message
     */
    public function __construct(User $user, string $message)
    {
        parent::__construct($user);
        $this->message = $message;
    }

    /**
     * Get the error message.
     *
     * @return string the message
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
