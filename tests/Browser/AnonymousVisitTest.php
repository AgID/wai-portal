<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Home;
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
            $browser->visit(new Home())
                    ->assertSee('Home')
                    ->waitForText('Entra con SPID')
                    ->assertSee('Entra con SPID')
                    ->clickLink('Dashboard')
                    ->assertSee("La risorsa richiesta richiede l'accesso.");
        });
    }
}
