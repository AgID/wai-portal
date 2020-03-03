<?php

namespace App\Events\PublicAdministration;

use App\Models\PublicAdministration;
use App\Models\User;

/**
 * Public Administration registered event.
 */
class PublicAdministrationRegistered extends AbstractPublicAdministrationEvent
{
    /**
     * Registering user event.
     *
     * @var User the user
     */
    protected $user;

    /**
     * Event constructor.
     *
     * @param PublicAdministration $publicAdministration the public administration
     * @param User $user the user
     */
    public function __construct(PublicAdministration $publicAdministration, User $user)
    {
        parent::__construct($publicAdministration);
        $this->user = $user;
    }

    /**
     * Get the registering user.
     *
     * @return User the user
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
