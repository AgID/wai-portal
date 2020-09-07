<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Tests\Browser\Traits\LighthouseTested;

class Home extends Page
{
    use LighthouseTested;

    /**
     * Get the URL for the page.
     *
     * @return string
     */
    public function url()
    {
        return '/';
    }

    /**
     * Assert that the browser is on the page.
     *
     * @param Browser $browser
     *
     * @return void
     */
    public function assert(Browser $browser)
    {
        parent::assertBase($browser);
        $browser->assertPathIs($this->url());
        $browser->assertSourceHas('"@type": "Organization"');
        $browser->assertSourceHas('"@type": "BreadcrumbList"');
        $this->lighthouseTest();
    }
}
