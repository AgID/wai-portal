<?php

namespace App\Events\Website;

use App\Models\User;
use App\Models\Website;

/**
 * Website added event.
 */
class WebsiteAdded extends AbstractWebsiteEvent
{
    private $user;

    public function __construct(Website $website, User $user)
    {
        parent::__construct($website);
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
