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
        $browser->assertSeeIn('@slim_header', __('ui.owner_short'));
        $browser->assertSeeIn('@slim_header', __('ui.partner_full'));
        $browser->assertSeeIn('@header', __('ui.site_title'));
        $browser->assertSeeIn('@header', __('ui.site_subtitle'));
        $browser->assertSeeLink('Dashboard');
        $browser->assertSeeIn('@footer', __('ui.owner_full'));
        $browser->assertSeeIn('@footer', strtoupper(__('ui.partner_full')));
        $browser->assertSeeLink(__('ui.footer_link_privacy'));
        $browser->assertSeeLink(__('ui.footer_link_legal_notes'));
    }

    /**
     * Get the global element shortcuts for the site.
     *
     * @return array
     */
    public static function siteElements()
    {
        return [
            '@slim_header' => '.Header-banner',
            '@header' => '.Header-navbar',
            '@footer' => '.Footer',
            '@spid_login_button' => '.agid-spid-enter',
            '@spid-idp-test_button' => '.agid-spid-idp-test',
        ];
    }
}
