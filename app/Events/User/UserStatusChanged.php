<?php

namespace App\Events\User;

use App\Enums\UserStatus;
use App\Models\User;

class UserStatusChanged extends AbstractUserEvent
{
    protected $oldStatus;

    public function __construct(User $user, int $oldStatus)
    {
        parent::__construct($user);
        $this->oldStatus = $oldStatus;
    }

    /**
     * @return UserStatus
     */
    public function getOldStatus(): UserStatus
    {
        return UserStatus::getInstance($this->oldStatus);
    }
}
