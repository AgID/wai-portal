<?php

namespace Tests\Feature;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class ScopeBouncerMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        Bouncer::dontCache();

        $this->user = factory(User::class)->create();

        Route::middleware('web')->get('_test/scope-bouncer', function () {
            return Response::HTTP_OK;
        });
    }

    public function testNoScopeEnforced(): void
    {
        $this->actingAs($this->user)
            ->get('_test/scope-bouncer');

        $this->assertEquals(0, Bouncer::scope()->get());
    }

    public function testScopeEnforced(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $publicAdministration->users()->sync([$this->user->id]);

        $this->actingAs($this->user)
            ->withSession(['tenant_id' => $publicAdministration->id])
            ->get('_test/scope-bouncer');

        $this->assertEquals($publicAdministration->id, Bouncer::scope()->get());
    }
}
