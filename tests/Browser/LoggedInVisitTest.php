<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\Pages\Dashboard;
use Tests\DuskTestCase;

class LoggedInVisitTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     *
     * @throws \Exception
     * @throws \Throwable
     *
     * @return void
     */
    public function testDashboard()
    {
        $this->addFakeUser();
        $this->assignFakeRole(1, 'admin');
        $this->injectFakeSpidSession();
        $this->browse(function (Browser $browser) {
            $browser->loginAs(User::find(1))
                    ->visit(new Dashboard())
                    ->assertSee('Dashboard');
        });
    }
}
