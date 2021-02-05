<?php

namespace App\Events\User;

use App\Models\PublicAdministration;
use App\Models\User;

/**
 * User invited event.
 */
class UserInvited extends AbstractUserEvent
{
    /**
     * The user issuing the invitation.
     *
     * @var User the user issuing the invitation
     */
    protected $invitedBy;

    /**
     * Create a new event instance.
     *
     * @param User $user the invited user
     * @param User $invitedBy the user issuing the invitation
     * @param PublicAdministration|null $publicAdministration the public administration selected for the invitation
     */
    public function __construct(User $user, User $invitedBy, ?PublicAdministration $publicAdministration = null)
    {
        parent::__construct($user, $publicAdministration);
        $this->invitedBy = $invitedBy;
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
