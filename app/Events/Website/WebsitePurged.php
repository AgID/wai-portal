<?php

namespace App\Events\Website;

/**
 * Website purged event.
 */
class WebsitePurged
{
    /**
     * JSON string representation of the purged website.
     *
     * @var string the JSON string
     */
    protected $websiteJson;

    /**
     * JSON string representation of the public administration the website belongs to.
     *
     * @var string the JSON string
     */
    protected $publicAdministrationJson;

    /**
     * Event constructor.
     *
     * @param string $websiteJson the JSON string of the website
     * @param string $publicAdministrationJson the JSON string of the public administration
     */
    public function __construct(string $websiteJson, string $publicAdministrationJson)
    {
        $this->websiteJson = $websiteJson;
        $this->publicAdministrationJson = $publicAdministrationJson;
    }

    /**
     * Get the JSON string representation of the website.
     *
     * @return string the JSON string
     */
    public function getWebsiteJson(): string
    {
        return $this->websiteJson;
    }

    /**
     * Get the JSON string representation of the public administration.
     *
     * @return string the JSON string
     */
    public function getPublicAdministrationJson(): string
    {
        return $this->publicAdministrationJson;
    }
}
