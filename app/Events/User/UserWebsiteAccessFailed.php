<?php

namespace App\Events\User;

use App\Models\User;
use App\Models\Website;

/**
 * User website access change failed event.
 */
class UserWebsiteAccessFailed extends AbstractUserEvent
{
    /**
     * The website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * The error message.
     *
     * @var string the message
     */
    protected $message;

    /**
     * Event constructor.
     *
     * @param User $user the user
     * @param Website $website the website
     * @param string $message the message
     */
    public function __construct(User $user, Website $website, string $message)
    {
        parent::__construct($user);
        $this->website = $website;
        $this->message = $message;
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
     * Get the error message.
     *
     * @return string the message
     */
    public function getMessage(): string
    {
        return $this->message;
    }
}
