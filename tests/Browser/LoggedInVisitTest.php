<?php

namespace Tests\Browser;

use App\Models\User;

use Tests\DuskTestCase;
use Tests\Browser\Pages\Dashboard;

use Laravel\Dusk\Browser;

class LoggedInVisitTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @return void
     * @throws \Exception
     * @throws \Throwable
     */
    public function testDashboard()
    {
        $this->addFakeUser();
        $this->assignFakeRole(1, 'admin');
        $this->injectFakeSpidSession();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit(new Dashboard)
                    ->assertSee('Dashboard');
        });
    }
}
