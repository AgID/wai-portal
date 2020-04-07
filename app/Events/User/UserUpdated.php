<?php

namespace App\Events\User;

use App\Models\User;

/**
 * User updated event.
 */
class UserUpdated extends AbstractUserEvent
{
    /**
     * Control of the update log.
     *
     * @var bool the value shows whether the only updated field is just 'last_access_at'
     */
    protected $onlyLastAccessDirty;

    /**
     * Event constructor.
     *
     * @param User $user the user
     */
    public function __construct(User $user)
    {
        parent::__construct($user);

        /*
        * getDirty must be equal to 2 because in addition to 'last_access_at' it also includes 'updated_at' field
        */
        $this->onlyLastAccessDirty = ($user->isDirty('last_access_at') && 2 === count($user->getDirty()));
    }

    /**
     * Get the value to check if the only updated field is 'last_access_at'.
     *
     * @return bool
     */
    public function isOnlyLastAccessDirty(): bool
    {
        return $this->onlyLastAccessDirty;
    }
}
