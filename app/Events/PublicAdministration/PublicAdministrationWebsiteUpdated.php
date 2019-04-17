<?php

namespace App\Events\PublicAdministration;

use App\Models\PublicAdministration;
use App\Models\Website;

/**
 * Public Administration primary website changed event.
 */
class PublicAdministrationWebsiteUpdated extends AbstractPublicAdministrationEvent
{
    /**
     * The current primary website.
     *
     * @var Website the current website reference
     */
    protected $primaryWebsite;

    /**
     * The new URL of the primary site.
     *
     * @var string the new URL
     */
    protected $newURL;

    /**
     * Event constructor.
     *
     * @param PublicAdministration $publicAdministration the public administration
     * @param Website $primaryWebsite the primary site
     * @param string $newURL the new URL
     */
    public function __construct(PublicAdministration $publicAdministration, Website $primaryWebsite, string $newURL)
    {
        parent::__construct($publicAdministration);
        $this->primaryWebsite = $primaryWebsite;
        $this->newURL = $newURL;
    }

    /**
     * Get the current primary website.
     *
     * @return Website the website reference
     */
    public function getPrimaryWebsite(): Website
    {
        return $this->primaryWebsite;
    }

    /**
     * Get the new URL.
     *
     * @return string the new URL
     */
    public function getNewURL(): string
    {
        return $this->newURL;
    }
}
