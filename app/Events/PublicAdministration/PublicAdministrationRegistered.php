<?php

namespace App\Events\PublicAdministration;

use App\Models\PublicAdministration;
use App\Models\User;

class PublicAdministrationRegistered extends AbstractPublicAdministrationEvent
{
    protected $user;

    public function __construct(PublicAdministration $publicAdministration, User $user)
    {
        parent::__construct($publicAdministration);
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
