<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Home;
use Tests\DuskTestCase;

class RegisterTest extends DuskTestCase
{
    protected function tearDown(): void
    {
        $this->deleteAnalyticsUser(1);
        parent::tearDown();
    }

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
                    ->clickLink('Dashboard')
                    ->assertPathIs('/spid/login')
                    ->waitForText('Entra con SPID')
                    ->click('@spid_login_button')
                    ->waitForText('Scegli il tuo provider SPID')
                    ->assertSee('Scegli il tuo provider SPID');
        });
        $this->injectFakeSpidSession();
        $this->browse(function (Browser $browser) {
            $browser->visit(new Home())
                ->clickLink('Dashboard')
                ->assertPathIs('/register')
                ->assertSee('Registrazione')
                ->type('email', 'nome.cognome@example.com')
                ->click('label[for="accept_terms"]')
                ->press('REGISTRA')
                ->assertSee("Una email di verifica è stata inviata all'indirizzo");
        });
        $signedUrl = $this->getSignedUrl(1);
        $this->browse(function (Browser $browser) use ($signedUrl) {
            $browser->visit($signedUrl)
                    ->assertPathIs('/dashboard/websites/add-primary')
                    ->visit('/user/verify')
                    ->waitForText("L'indirizzo email dell'utente")
                    ->assertSee('è già stato verificato.');
        });
    }
}
