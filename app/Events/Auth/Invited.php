<?php

namespace App\Events\Auth;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Queue\SerializesModels;

class Invited
{
    use SerializesModels;

    /**
     * The invitated user.
     *
     * @var User
     */
    public $user;

    /**
     * The public administration selected for the invitation.
     *
     * @var App\Models\PublicAdministration
     */
    public $publicAdministration;

    /**
     * The user issuing the invitation.
     *
     * @var App\Models\User
     */
    public $invitedBy;

    /**
     * Create a new event instance.
     *
     * @param App\Models\User $user
     * @param App\Models\User $invitedBy
     * @param App\Models\PublicAdministration $publicAdministration
     *
     * @return void
     */
    public function __construct(User $user, PublicAdministration $publicAdministration = null, User $invitedBy)
    {
        $this->user = $user;
        $this->publicAdministration = $publicAdministration;
        $this->invitedBy = $invitedBy;
    }
}
