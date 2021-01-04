<?php

namespace App\Events\User;

use App\Models\PublicAdministration;
use App\Models\User;

/**
 * User email changed event.
 */
class UserEmailForPublicAdministrationChanged extends AbstractUserEvent
{
    /**
     * The updated email address.
     *
     * @var string the updated email address
     */
    protected $updatedEmail;

    /**
     * Event constructor.
     *
     * @param User $user the user
     * @param PublicAdministration|null $publicAdministration the public administration
     * @param string $updatedEmail the updated email address
     */
    public function __construct(User $user, ?PublicAdministration $publicAdministration = null, string $updatedEmail)
    {
        parent::__construct($user, $publicAdministration);
        $this->updatedEmail = $updatedEmail;
    }

    /**
     * Get the updated email address.
     *
     * @return string the user updated email address
     */
    public function getUpdatedEmail(): string
    {
        return $this->updatedEmail;
    }
}
