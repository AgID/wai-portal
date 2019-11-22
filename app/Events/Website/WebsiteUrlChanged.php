<?php

namespace App\Events\Website;

use App\Models\Website;

/**
 * Website URL changed event.
 */
class WebsiteUrlChanged extends AbstractWebsiteEvent
{
    /**
     * The previous URL.
     *
     * @var string the URL
     */
    private $oldUrl;

    /**
     * Event constructor.
     *
     * @param Website $website the changed website
     * @param string $oldUrl the previous URL
     */
    public function __construct(Website $website, string $oldUrl)
    {
        parent::__construct($website);
        $this->oldUrl = $oldUrl;
    }

    /**
     * Get the previous URL.
     *
     * @return string the URL
     */
    public function getOldUrl(): string
    {
        return $this->oldUrl;
    }
}
