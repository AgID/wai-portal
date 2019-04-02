<?php

namespace App\Events\PublicAdministration;

use App\Models\PublicAdministration;
use App\Models\Website;
use Illuminate\Queue\SerializesModels;

/**
 * Public Administration primary website changed event.
 */
class PublicAdministrationWebsiteUpdated
{
    use SerializesModels;

    /**
     * The changed public administration.
     *
     * @var PublicAdministration the public administration reference
     */
    protected $publicAdministration;

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
        $this->publicAdministration = $publicAdministration;
        $this->primaryWebsite = $primaryWebsite;
        $this->newURL = $newURL;
    }

    /**
     * Get the changed Public Administration.
     *
     * @return PublicAdministration the public administration reference
     */
    public function getPublicAdministration(): PublicAdministration
    {
        return $this->publicAdministration;
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
