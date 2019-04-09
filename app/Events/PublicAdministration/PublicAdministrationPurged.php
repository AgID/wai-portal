<?php

namespace App\Events\PublicAdministration;

/**
 * Public Administration purged event.
 */
class PublicAdministrationPurged
{
    /**
     * JSON string representation of the purged public administration.
     *
     * @var string the JSON string
     */
    protected $publicAdministration;

    /**
     * Event constructor.
     *
     * @param string $publicAdministration the JSON string of the public administration
     */
    public function __construct(string $publicAdministration)
    {
        $this->publicAdministration = $publicAdministration;
    }

    /**
     * Get the JSON string representation.
     *
     * @return string the JSON string
     */
    public function getPublicAdministration(): string
    {
        return $this->publicAdministration;
    }
}
