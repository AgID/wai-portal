<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AdminUserTest extends DuskTestCase
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
        $this->addFakeUser();
        $mailAddress = 'nome.cognome@gov.it';
        $newPassword = 'Password.1'; // really?
        $this->browse(function (Browser $browser) use ($mailAddress) {
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
                    ->type('email', $mailAddress)
                    ->press('INVIA')
                    ->assertSee('Il nuovo utente è stato invitato come amministratore al progetto Web Analytics Italia.')
                    ->visit('/admin/user/logout')
                    ->assertPathIs('/');
        });
        $this->setPassword(2, 'randomPassword');
        $signedUrl = $this->getSignedUrl(2);
        $this->browse(function (Browser $browser) use ($mailAddress, $newPassword, $signedUrl) {
            $browser->visit($signedUrl)
                    ->assertPathIs('/admin/user/login')
                    ->assertSee('Accesso amministratori')
                    ->type('email', $mailAddress)
                    ->type('password', 'randomPassword')
                    ->press('ACCEDI')
                    ->assertPathIs('/admin/user/change-password')
                    ->assertSee('La password è scaduta e deve essere cambiata.')
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press('CAMBIA PASSWORD')
                    ->assertPathIs('/admin/user/change-password')
                    ->assertSee('La password scelta non è sufficientemente complessa.')
                    ->type('password', $newPassword)
                    ->type('password_confirmation', $newPassword)
                    ->press('CAMBIA PASSWORD')
                    ->assertPathIs('/admin/dashboard')
                    ->assertSee('La password è stata cambiata.')
                    ->visit('/admin/user/logout')
                    ->assertPathIs('/');
        });
        $this->browse(function (Browser $browser) use ($mailAddress, $newPassword) {
            $browser->visit('/admin')
                    ->assertPathIs('/admin/user/login')
                    ->type('email', $mailAddress)
                    ->type('password', $newPassword)
                    ->press('ACCEDI')
                    ->assertPathIs('/admin/dashboard')
                    ->assertSee('Dashboard amministrativa');
        });
    }
}
