<?php

namespace App\Events\Jobs;

/**
 * Purge old pending invitations completed event.
 */
class PurgePendingInvitationsCompleted
{
    /**
     * Purged users list.
     * The array follows this schema:
     * array[]
     *  ['users'].
     *
     * @var array the purged users list
     */
    protected $purged;

    /**
     * The list of invited users still pending.
     * The array follows this schema:
     * array[]
     *  ['users'].
     *
     * @var array the invited users still pending
     */
    protected $pending;

    /**
     * Event constructor.
     *
     * @param array $purged the purged users array
     * @param array $pending the invited users still pending array
     */
    public function __construct(array $purged, array $pending)
    {
        $this->purged = $purged;
        $this->pending = $pending;
    }

    /**
     * Get the array of the purged users.
     *
     * @return array the purged users list
     */
    public function getPurged(): array
    {
        return $this->purged;
    }

    /**
     * Get the array of the invited users still pending.
     *
     * @return array the invited users still pending list
     */
    public function getPending(): array
    {
        return $this->pending;
    }
}
