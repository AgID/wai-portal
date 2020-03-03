<?php

namespace App\Events\User;

use App\Enums\WebsiteAccessType;
use App\Models\User;
use App\Models\Website;

/**
 * User website access changed event.
 */
class UserWebsiteAccessChanged extends AbstractUserEvent
{
    /**
     * The website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * The new access level.
     *
     * @var WebsiteAccessType the access level
     */
    protected $accessType;

    /**
     * Event constructor.
     *
     * @param User $user the user
     * @param Website $website the website
     * @param WebsiteAccessType $accessType the new access level
     */
    public function __construct(User $user, Website $website, WebsiteAccessType $accessType)
    {
        parent::__construct($user);
        $this->website = $website;
        $this->accessType = $accessType;
    }

    /**
     * Get the website.
     *
     * @return Website the website
     */
    public function getWebsite(): Website
    {
        return $this->website;
    }

    /**
     * Get the access level.
     *
     * @return WebsiteAccessType the access level
     */
    public function getAccessType(): WebsiteAccessType
    {
        return $this->accessType;
    }
}
