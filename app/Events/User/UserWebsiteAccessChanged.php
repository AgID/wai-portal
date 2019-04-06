<?php

namespace App\Events\User;

use App\Enums\WebsiteAccessType;
use App\Events\User\Contracts\UserEvent;
use App\Models\User;
use App\Models\Website;

class UserWebsiteAccessChanged extends UserEvent
{
    protected $website;

    protected $accessType;

    /**
     * UserWebsiteAccessChanged constructor.
     *
     * @param User $user
     * @param Website $website
     * @param WebsiteAccessType $accessType
     */
    public function __construct(User $user, Website $website, WebsiteAccessType $accessType)
    {
        parent::__construct($user);
        $this->website = $website;
        $this->accessType = $accessType;
    }

    /**
     * @return Website
     */
    public function getWebsite(): Website
    {
        return $this->website;
    }

    /**
     * @return WebsiteAccessType
     */
    public function getAccessType(): WebsiteAccessType
    {
        return $this->accessType;
    }
}
