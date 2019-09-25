<?php

namespace Tests\Browser\Pages;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Page as BasePage;

abstract class Page extends BasePage
{
    /**
     * Assert base tests valid on every page.
     *
     * @param Browser $browser
     *
     * @return void
     */
    public function assertBase(Browser $browser)
    {
        $browser->resize(1024, 768);
        $browser->assertSeeIn('@slim_header', __(config('site.owner.name')));
        $browser->assertSeeIn('@header', config('app.name'));
        $browser->assertSeeIn('@footer', config('app.name'));
        $browser->assertSeeLink(__(config('site.footer_links.0.name')));
    }

    /**
     * Get the global element shortcuts for the site.
     *
     * @return array
     */
    public static function siteElements()
    {
        return [
            '@slim_header' => '.it-header-slim-wrapper',
            '@header' => '.it-header-center-wrapper',
            '@footer' => '.it-footer',
            '@spid_login_button' => '.agid-spid-enter',
            '@spid-idp-test_button' => '.agid-spid-idp-test',
        ];
    }
}
