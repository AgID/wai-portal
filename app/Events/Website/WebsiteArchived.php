<?php

namespace App\Events\Website;

use App\Models\Website;

/**
 * Website archived event.
 */
class WebsiteArchived extends AbstractWebsiteEvent
{
    /**
     * Whether the website was archived manually.
     *
     * @var bool true if archived manually
     */
    protected $manual;

    /**
     * Event constructor.
     *
     * @param Website $website the archived website
     * @param bool $manual whether the website was archived manually
     */
    public function __construct(Website $website, bool $manual = false)
    {
        parent::__construct($website);
        $this->manual = $manual;
    }

    /**
     * Check is the website was archived manually.
     *
     * @return bool true if archived manually
     */
    public function isManual(): bool
    {
        return $this->manual;
    }
}
