<?php

namespace App\Events\Jobs;

/**
 * Public administration update from IPA index job completed event.
 */
class PublicAdministrationsUpdateFromIpaCompleted
{
    /**
     * The updates list.
     *
     * @var array the updates list
     */
    protected $updates;

    /**
     * Event constructor.
     *
     * @param array $updates the updates list
     */
    public function __construct(array $updates = [])
    {
        $this->updates = $updates;
    }

    /**
     * Get the updates list.
     * The array follows this schema:
     * array[]
     *  [ipaCode]
     *      [changedAttributeName]
     *          ['old']
     *          ['new'].
     *
     * @return array the updates list or an empty array if none
     */
    public function getUpdates(): array
    {
        return $this->updates;
    }
}
