<?php

namespace App\Events\Jobs;

/**
 * IPA update job completed event.
 */
class IPAUpdateCompleted
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
