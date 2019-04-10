<?php

namespace App\Events\User;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

/**
 * User event contract.
 */
abstract class AbstractUserEvent
{
    use SerializesModels;

    /**
     * The changed user.
     *
     * @var User the user
     */
    protected $user;

    /**
     * Event constructor.
     *
     * @param User $user the user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the user.
     *
     * @return User the user
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
