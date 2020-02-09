<?php

namespace App\Events\Website;

use App\Models\User;
use App\Models\Website;

/**
 * Website added event.
 */
class WebsiteAdded extends AbstractWebsiteEvent
{
    /**
     * The user who added the website.
     *
     * @var User the user
     */
    private $user;

    /**
     * Event constructor.
     *
     * @param Website $website the added website
     * @param User $user the user who added the website
     */
    public function __construct(Website $website, User $user)
    {
        parent::__construct($website);
        $this->user = $user;
    }

    /**
     * Get the user who added the website.
     *
     * @return User the user
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
