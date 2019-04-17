<?php

namespace App\Events\Website;

use App\Models\Website;
use Illuminate\Queue\SerializesModels;

/**
 * Website event contract.
 */
abstract class AbstractWebsiteEvent
{
    use SerializesModels;

    /**
     * The changed website.
     *
     * @var Website the website
     */
    protected $website;

    /**
     * Event constructor.
     *
     * @param Website $website the changed website
     */
    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    /**
     * Get the changed website.
     *
     * @return Website the website
     */
    public function getWebsite(): Website
    {
        return $this->website;
    }
}
