<?php

namespace App\Events\User;

use App\Models\PublicAdministration;
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
     * The public administration the user belongs to.
     *
     * @var PublicAdministration the public administration
     */
    protected $publicAdministration;

    /**
     * Event constructor.
     *
     * @param User $user the user
     * @param PublicAdministration|null $publicAdministration the public administration
     */
    public function __construct(User $user, ?PublicAdministration $publicAdministration = null)
    {
        $this->user = $user;
        $this->publicAdministration = $publicAdministration;
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

    /**
     * Get the public administration.
     *
     * @return PublicAdministration|null the public administration
     */
    public function getPublicAdministration(): ?PublicAdministration
    {
        return $this->publicAdministration;
    }
}
