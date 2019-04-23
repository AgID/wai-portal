<?php

namespace App\Events\Website;

use App\Models\Website;

/**
 * Website archiving event.
 */
class WebsiteArchiving extends AbstractWebsiteEvent
{
    protected $daysLeft;

    public function __construct(Website $website, int $daysLeft)
    {
        parent::__construct($website);
        $this->daysLeft = $daysLeft;
    }

    /**
     * @return int
     */
    public function getDaysLeft(): int
    {
        return $this->daysLeft;
    }
}
