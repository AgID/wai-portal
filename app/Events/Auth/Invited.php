<?php

namespace App\Events\Auth;

use Illuminate\Auth\Events\Registered;
use Illuminate\Queue\SerializesModels;

class Invited extends Registered
{
    use SerializesModels;

    /**
     * The user issuing the invitation.
     *
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $invitedBy;

    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     *
     * @return void
     */
    public function __construct($user, $invitedBy)
    {
        $this->user = $user;
        $this->invitedBy = $invitedBy;
    }
}
