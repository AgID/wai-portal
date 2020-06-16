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

    /**
     * The user who registered the public administration.
     *
     * @var User the user
     */
    protected $user;

    /**
     * string email for the user in the public administration.
     *
     * @var string the JSON string
     */
    protected $userEmailForPublicAdministration;

    /**
     * Event constructor.
     *
     * @param string $publicAdministrationJson the JSON string of the public administration
     * @param User $user the user who registered the public administration
     * @param string $userEmailForPublicAdministration the email used by the user in this public administration
     */
    public function __construct(string $publicAdministrationJson, User $user, ?string $userEmailForPublicAdministration = null)
    {
        $this->publicAdministrationJson = $publicAdministrationJson;
        $this->user = $user;
        $this->userEmailForPublicAdministration = $userEmailForPublicAdministration;
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

    /**
     * Get the user who registered the public administration.
     *
     * @return User the user
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * Get the email used by the user in this public administration.
     *
     * @return string the email
     */
    public function getUserEmailForPublicAdministration(): string
    {
        return $this->userEmailForPublicAdministration;
    }
}
