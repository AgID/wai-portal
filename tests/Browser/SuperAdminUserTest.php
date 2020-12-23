<?php

namespace Tests\Browser;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\AdminDashboard;
use Tests\DuskTestCase;

class SuperAdminUserTest extends DuskTestCase
{
    /**
     * Pre-test setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('db:seed');
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
        $mailAddress = 'nuovo.superadmin@webanalytics.italia.it';
        $newPassword = 'Password.1';
        $this->browse(function (Browser $browser) use ($mailAddress) {
            $browser->visit('/admin')
                    ->assertPathIs('/admin/user/login')
                    ->assertSee(__('Accesso super amministratori'))
                    ->type('email', 'nome.cognome@example.com')
                    ->type('password', 'password')
                    ->press(__('Accedi'))
                    ->waitForText(__('Dashboard amministrazioni'))
                    ->assertPathIs('/admin/dashboard')
                    ->visit('/admin/users/create')
                    ->assertSee(__('Aggiungi un utente super amministratore'))
                    ->type('name', 'Nome')
                    ->type('family_name', 'Cognome')
                    ->type('email', $mailAddress)
                    ->press(__('Salva'))
                    ->waitForText(__('Nuovo utente super amministratore creato'))
                    ->visit('/admin/user/logout')
                    ->assertPathIs('/');
        });

        $user = User::find(2);
        $user->password = Hash::make('randomPassword');
        $user->save();
        $signedUrl = $this->getSignedUrl(2);
        $this->browse(function (Browser $browser) use ($mailAddress, $newPassword, $signedUrl) {
            $browser->visit($signedUrl)
                    ->assertPathIs('/admin/user/login')
                    ->assertSee(__('Accesso super amministratori'))
                    ->type('email', $mailAddress)
                    ->type('password', 'randomPassword')
                    ->press(__('Accedi'))
                    ->assertPathIs('/admin/user/change-password')
                    ->waitForText(__('La password è scaduta e deve essere cambiata.'))
                    ->type('password', 'password')
                    ->type('password_confirmation', 'password')
                    ->press(__('Cambia password'))
                    ->assertPathIs('/admin/user/change-password')
                    ->assertSee(__('La password scelta non è abbastanza complessa.'))
                    ->type('password', $newPassword)
                    ->type('password_confirmation', $newPassword)
                    ->press(__('Cambia password'))
                    ->assertPathIs('/admin/dashboard')
                    ->waitForText(__('La password è stata cambiata.'))
                    ->visit('/admin/user/logout')
                    ->assertPathIs('/');
        });
        $this->browse(function (Browser $browser) use ($mailAddress, $newPassword) {
            $browser->visit('/admin')
                    ->assertPathIs('/admin/user/login')
                    ->type('email', $mailAddress)
                    ->type('password', $newPassword)
                    ->press(__('Accedi'))
                    ->assertPathIs('/admin/dashboard')
                    ->visit(new AdminDashboard())
                    ->assertSee(__('Dashboard amministrazioni'));
        });
    }
}
