<?php

namespace App\Events\Website\Contracts;

use App\Models\Website;
use Illuminate\Queue\SerializesModels;

abstract class WebsiteEvent
{
    use SerializesModels;

    protected $website;

    public function __construct(Website $website)
    {
        $this->website = $website;
    }

    /**
     * @return Website
     */
    public function getWebsite(): Website
    {
        return $this->website;
    }
}
