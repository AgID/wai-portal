<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;

class AdminUserTest extends DuskTestCase
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
        $this->addFakeUser();
        $mail_address = 'nome.cognome@gov.it';
        $password = 'Password.1';
        $this->browse(function (Browser $browser) use ($mail_address) {
            $browser->visit('/admin')
                    ->assertPathIs('/admin/user/login')
                    ->assertSee('Accesso amministratori')
                    ->type('email', 'nome.cognome@example.com')
                    ->type('password', 'password')
                    ->press('ACCEDI')
                    ->assertPathIs('/admin/dashboard')
                    ->assertSee('Dashboard amministrativa')
                    ->assertSee('Area amministrativa')
                    ->visit('/admin/users/add')
                    ->assertSee('Nuovo utente amministratore')
                    ->type('name', 'Nome')
                    ->type('familyName', 'Cognome')
                    ->type('email', $mail_address)
                    ->press('INVIA')
                    ->assertSee('Il nuovo utente è stato invitato come amministratore al progetto Web Analytics Italia.')
                    ->visit('/admin/user/logout')
                    ->assertPathIs('/');
        });
        $verificationToken = $this->getVerificationToken(2);
        $this->browse(function (Browser $browser) use ($mail_address, $password, $verificationToken) {
            $browser->visit('/admin/user/verify')
                    ->type('email', $mail_address)
                    ->type('token', $verificationToken)
                    ->press('CONFERMA')
                    ->assertPathIs('/admin/user/change-password')
                    ->assertSee("L'indirizzo email è stato verificato correttamente.")
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press('CAMBIA PASSWORD')
                    ->assertPathIs('/admin/user/change-password')
                    ->assertSee('La password scelta non è sufficientemente complessa.')
                    ->type('password', $password)
                    ->type('password_confirmation', $password)
                    ->press('CAMBIA PASSWORD')
                    ->assertPathIs('/admin/dashboard')
                    ->assertSee('La password è stata cambiata.')
                    ->visit('/admin/user/logout')
                    ->assertPathIs('/');
        });
        $this->browse(function (Browser $browser) use ($mail_address, $password) {
            $browser->visit('/admin')
                    ->assertPathIs('/admin/user/login')
                    ->type('email', $mail_address)
                    ->type('password', $password)
                    ->press('ACCEDI')
                    ->assertPathIs('/admin/dashboard')
                    ->assertSee('Dashboard amministrativa');
        });
    }
}
