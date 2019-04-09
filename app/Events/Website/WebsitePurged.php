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
    protected $website;

    /**
     * Event constructor.
     *
     * @param string $website the JSON string of the website
     */
    public function __construct(string $website)
    {
        $this->website = $website;
    }

    /**
     * Get the JSON string representation.
     *
     * @return string the JSON string
     */
    public function getWebsite(): string
    {
        return $this->website;
    }
}
