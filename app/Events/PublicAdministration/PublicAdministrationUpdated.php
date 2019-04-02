<?php

namespace App\Events\PublicAdministration;

use App\Models\PublicAdministration;
use Illuminate\Queue\SerializesModels;

/**
 * Public Administration updated event.
 */
class PublicAdministrationUpdated
{
    use SerializesModels;

    /**
     * The changed public administration.
     *
     * @var PublicAdministration the public administration reference
     */
    protected $publicAdministration;

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
        $this->publicAdministration = $publicAdministration;
        $this->updates = $updates;
    }

    /**
     * Get the changed Public Administration.
     *
     * @return PublicAdministration
     */
    public function getPublicAdministration(): PublicAdministration
    {
        return $this->publicAdministration;
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
