<?php

namespace App\Events\Jobs;

/**
 * Purge orphaned entities completed event.
 */
class PurgeOrphanedEntitiesCompleted
{
    /**
     * The list of processed entities.
     * The array follows this schema:
     * array[]
     *  ['entity_type']
     *    array[].
     *
     * @var array the near-to-be archived websites list
     */
    protected $processed;

    /**
     * Event constructor.
     *
     * @param array $processed the processed entities array
     */
    public function __construct(array $processed)
    {
        $this->processed = $processed;
    }

    /**
     * Get the array of processed entities.
     *
     * @return array the entities list
     */
    public function getProcessed(): array
    {
        return $this->processed;
    }
}
