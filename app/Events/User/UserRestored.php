<?php

namespace App\Events\User;

use App\Models\PublicAdministration;
use App\Models\User;

/**
 * User restored event.
 */
class UserRestored extends AbstractUserEvent
{
    /**
     * Event constructor.
     *
     * @param User $user the user
     * @param PublicAdministration $publicAdministration the public administration
     */
    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    /**
     * Get the public administration.
     *
     * @return PublicAdministration the public administration
     */
    public function getPublicAdministration(): PublicAdministration
    {
        return $this->publicAdministration;
    }
}
