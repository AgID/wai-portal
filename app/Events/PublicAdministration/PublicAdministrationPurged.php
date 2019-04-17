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
    protected $publicAdministrationJson;

    /**
     * Event constructor.
     *
     * @param string $publicAdministrationJson the JSON string of the public administration
     */
    public function __construct(string $publicAdministrationJson)
    {
        $this->publicAdministrationJson = $publicAdministrationJson;
    }

    /**
     * Get the JSON string representation.
     *
     * @return string the JSON string
     */
    public function getPublicAdministrationJson(): string
    {
        return $this->publicAdministrationJson;
    }
}
