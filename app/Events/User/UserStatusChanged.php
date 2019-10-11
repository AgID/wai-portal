<?php

namespace App\Events\User;

use App\Enums\UserStatus;
use App\Models\User;

/**
 * User status changed event.
 */
class UserStatusChanged extends AbstractUserEvent
{
    /**
     * Previous user status.
     *
     * @var int the previous status value
     */
    protected $oldStatus;

    /**
     * Event constructor.
     *
     * @param User $user the user
     * @param int $oldStatus the previous status value
     */
    public function __construct(User $user, int $oldStatus)
    {
        parent::__construct($user);
        $this->oldStatus = $oldStatus;
    }

    /**
     * Get the previous user status.
     *
     * @return UserStatus the previous status
     */
    public function getOldStatus(): UserStatus
    {
        return UserStatus::getInstance($this->oldStatus);
    }
}
