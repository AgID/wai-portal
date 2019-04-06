<?php

namespace App\Events\User\Contracts;

use App\Models\User;
use Illuminate\Queue\SerializesModels;

abstract class UserEvent
{
    use SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
}
