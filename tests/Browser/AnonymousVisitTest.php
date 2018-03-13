<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Tests\Browser\Pages\Home;

use Laravel\Dusk\Browser;

class AnonymousVisitTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testVisit()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit(new Home)
                    ->assertSee('Home')
                    ->waitForText('Entra con SPID')
                    ->assertSee('Entra con SPID')
                    ->clickLink('Dashboard')
                    ->assertSee("La risorsa richiesta richiede l'accesso.");
        });
    }
}
