<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Faqs;
use Tests\Browser\Pages\Home;
use Tests\Browser\Pages\HowToJoin;
use Tests\DuskTestCase;

class AnonymousVisitTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @throws \Exception
     * @throws \Throwable
     *
     * @return void
     */
    public function testVisit()
    {
        $this->browse(function (Browser $browser) {
            $metaDescription = config('site.meta.description');

            $browser->visit(new Home())
                ->assertSourceHas('<meta name="description" content="' . $metaDescription . '">')
                ->assertSee('Le statistiche dei siti web')
                ->waitForText('Entra con SPID')
                ->assertSee('Entra con SPID')
                ->visit('/analytics')
                ->assertSee(__("La pagina che hai richiesto è raggiungibile solo dopo l'autenticazione."))
                ->assertSourceHas('<meta name="description" content="' . $metaDescription . '">')
                ->assertSourceHas('<meta property="og:description" content="' . $metaDescription . '">')
                ->assertSourceHas('<meta property="og:image" content="' . asset(config('site.meta.image')) . '">');

            $browser->visit(new Faqs())
                ->assertSourceHas('<script type="application/ld+json">');

            $browser->visit(new HowToJoin())
                ->assertSourceHas('<script type="application/ld+json">');
        });
    }
}
