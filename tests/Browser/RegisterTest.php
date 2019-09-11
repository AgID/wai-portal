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
                    ->visit('/dashboard')
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
                // NOTE: workaround to interact with a bootstrap checkbox with a link in its label
                ->waitUntil('$("input[name=accept_terms]").prop("checked", true)')
                ->press(__('Registrati'))
                ->assertSee(__('Abbiamo inviato un link di conferma al tuo indirizzo'));
        });
        $signedUrl = $this->getSignedUrl(1);
        $this->browse(function (Browser $browser) use ($signedUrl) {
            $browser->visit($signedUrl)
                    ->assertPathIs('/websites')
                    ->visit('/user/verify')
                    ->waitForText("L'indirizzo email")
                    ->assertSee('è già stato verificato');
        });
    }
}
