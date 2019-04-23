<?php

namespace App\Events\Website;

use App\Models\Website;

/**
 * Website archiving event.
 */
class WebsiteArchiving extends AbstractWebsiteEvent
{
    /**
     * The number of days left before automatic archiving.
     *
     * @var int the number of days
     */
    protected $daysLeft;

    /**
     * Event constructor.
     *
     * @param Website $website the changed website
     * @param int $daysLeft the number of days left
     */
    public function __construct(Website $website, int $daysLeft)
    {
        parent::__construct($website);
        $this->daysLeft = $daysLeft;
    }

    /**
     * Get the number of days left before automatic archiving.
     *
     * @return int the number of days left
     */
    public function getDaysLeft(): int
    {
        return $this->daysLeft;
    }
}
