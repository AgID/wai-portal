<?php

namespace App\Events\PublicAdministration;

use App\Events\PublicAdministration\Contracts\PublicAdministrationEvent;
use App\Models\PublicAdministration;

/**
 * Public Administration updated event.
 */
class PublicAdministrationUpdated extends PublicAdministrationEvent
{
    /**
     * The changes list.
     *
     * @var array the list
     */
    protected $updates;

    /**
     * Event constructor.
     *
     * @param PublicAdministration $publicAdministration the public administration
     * @param array $updates the updates list
     */
    public function __construct(PublicAdministration $publicAdministration, array $updates)
    {
        parent::__construct($publicAdministration);
        $this->updates = $updates;
    }

    /**
     * Get the updates list.
     * The array follows this schema:
     * array[]
     *  [changedAttributeName]
     *      ['old']
     *      ['new'].
     *
     * @return array
     */
    public function getUpdates(): array
    {
        return $this->updates;
    }
}
