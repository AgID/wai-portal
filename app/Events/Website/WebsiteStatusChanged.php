<?php

namespace App\Events\Website;

use App\Enums\WebsiteStatus;
use App\Models\Website;

/**
 * Website status changed event.
 */
class WebsiteStatusChanged extends AbstractWebsiteEvent
{
    /**
     * Previous website status.
     *
     * @var int the previous status value
     */
    protected $oldStatus;

    /**
     * Event constructor.
     *
     * @param Website $website the website
     * @param int $oldStatus the previous status value
     */
    public function __construct(Website $website, int $oldStatus)
    {
        parent::__construct($website);
        $this->oldStatus = $oldStatus;
    }

    /**
     * Get the previous website status.
     *
     * @return WebsiteStatus the previous status
     */
    public function getOldStatus(): WebsiteStatus
    {
        return WebsiteStatus::getInstance($this->oldStatus);
    }
}
