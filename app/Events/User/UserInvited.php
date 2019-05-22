<?php

namespace App\Events\User;

use App\Models\PublicAdministration;
use App\Models\User;

class UserInvited extends AbstractUserEvent
{
    /**
     * The public administration selected for the invitation.
     *
     * @var PublicAdministration
     */
    protected $publicAdministration;

    /**
     * The user issuing the invitation.
     *
     * @var User
     */
    protected $invitedBy;

    /**
     * Create a new event instance.
     *
     * @param User $user the invited user
     * @param User $invitedBy the user issuing the invitation
     * @param PublicAdministration $publicAdministration the public administration selected for the invitation
     *
     * @return void
     */
    public function __construct(User $user, PublicAdministration $publicAdministration = null, User $invitedBy)
    {
        $this->user = $user;
        $this->publicAdministration = $publicAdministration;
        $this->invitedBy = $invitedBy;
    }

    /**
     * Get the public administration.
     *
     * @return PublicAdministration the public administration
     */
    public function getPublicAdministration(): ?PublicAdministration
    {
        return $this->publicAdministration;
    }

    /**
     * Get the user issuing the invitation.
     *
     * @return User the user issuing the invitation
     */
    public function getInvitedBy(): User
    {
        return $this->invitedBy;
    }
}
