<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class RedirectIfAuthenticatedMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();

        Bouncer::dontCache();

        Route::middleware('guest')->get('_test/guest', function () {
            return Response::HTTP_OK;
        });
    }

    public function testUserNotAuthenticated(): void
    {
        $this->get('_test/guest')
            ->assertOk();
    }

    public function testUserAuthenticatedRedirect(): void
    {
        $this->actingAs($this->user)
            ->get('_test/guest')
            ->assertRedirect(route('dashboard'));

        $this->user->allow(UserPermission::ACCESS_ADMIN_AREA);

        $this->actingAs($this->user)
            ->get('_test/guest')
            ->assertRedirect(route('admin.dashboard'));
    }
}
