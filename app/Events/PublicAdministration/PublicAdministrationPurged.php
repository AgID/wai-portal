<?php

namespace App\Events\PublicAdministration;

use App\Models\User;

/**
 * Public Administration purged event.
 */
class PublicAdministrationPurged
{
    /**
     * JSON string representation of the purged public administration.
     *
     * @var string the JSON string
     */
    protected $publicAdministrationJson;

    protected $user;

    /**
     * Event constructor.
     *
     * @param string $publicAdministrationJson the JSON string of the public administration
     */
    public function __construct(string $publicAdministrationJson, User $user)
    {
        $this->publicAdministrationJson = $publicAdministrationJson;
        $this->user = $user;
    }

    /**
     * Get the JSON string representation.
     *
     * @return string the JSON string
     */
    public function getPublicAdministrationJson(): string
    {
        return $this->publicAdministrationJson;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
