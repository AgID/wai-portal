<?php

namespace App\Events\User;

use App\Events\User\Contracts\UserEvent;
use App\Models\User;
use App\Models\Website;

class UserWebsiteAccessFailed extends UserEvent
{
    protected $website;

    protected $message;

    public function __construct(User $user, Website $website, string $message)
    {
        parent::__construct($user);
        $this->website = $website;
        $this->message = $message;
    }

    /**
     * @return Website
     */
    public function getWebsite(): Website
    {
        return $this->website;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
