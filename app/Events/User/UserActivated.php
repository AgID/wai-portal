<?php

namespace App\Events\User;

use App\Models\PublicAdministration;
use App\Models\User;

/**
 * User activated event.
 */
class UserActivated extends AbstractUserEvent
{
    /**
     * The public administration the user belongs to.
     *
     * @var PublicAdministration the public administration
     */
    protected $publicAdministration;

    /**
     * Event constructor.
     *
     * @param User $user the user
     * @param PublicAdministration $publicAdministration the public administration
     */
    public function __construct(User $user, PublicAdministration $publicAdministration)
    {
        parent::__construct($user);
        $this->publicAdministration = $publicAdministration;
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
