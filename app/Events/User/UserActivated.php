<?php

namespace App\Events\User;

use App\Models\PublicAdministration;
use App\Models\User;

/**
 * User activated event.
 */
class UserActivated extends AbstractUserEvent
{
    protected $publicAdministration;

    public function __construct(User $user, PublicAdministration $publicAdministration)
    {
        parent::__construct($user);
        $this->publicAdministration = $publicAdministration;
    }

    /**
     * @return PublicAdministration
     */
    public function getPublicAdministration(): PublicAdministration
    {
        return $this->publicAdministration;
    }
}
