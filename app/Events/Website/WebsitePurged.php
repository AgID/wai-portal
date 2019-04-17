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
     * Event constructor.
     *
     * @param string $websiteJson the JSON string of the website
     */
    public function __construct(string $websiteJson)
    {
        $this->websiteJson = $websiteJson;
    }

    /**
     * Get the JSON string representation.
     *
     * @return string the JSON string
     */
    public function getWebsiteJson(): string
    {
        return $this->websiteJson;
    }
}
