<?php

namespace App\Events\Website;

class WebsitePurged
{
    protected $website;

    public function __construct(string $website)
    {
        $this->website = $website;
    }

    /**
     * @return string
     */
    public function getWebsite(): string
    {
        return $this->website;
    }
}
