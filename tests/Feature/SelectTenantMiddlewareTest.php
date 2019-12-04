<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Http\Middleware\SelectTenant;
use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class SelectTenantMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        Bouncer::dontCache();

        Route::middleware([SelectTenant::class, 'web'])->get('_test/tenant-select/{publicAdministration?}', function (?PublicAdministration $publicAdministration) {
            return Response::HTTP_OK;
        });
    }

    public function testNotAuthenticated(): void
    {
        $this->get('_test/tenant-select')
            ->assertSessionMissing('tenant_id')
            ->assertOk();
    }

    public function testUserTenantEnforced(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $publicAdministration->users()->sync([$this->user->id]);

        $this->actingAs($this->user)
            ->get('_test/tenant-select')
            ->assertSessionHas('tenant_id', $publicAdministration->id);
    }

    public function testSuperUserTenantNotEnforce(): void
    {
        $this->user->assign(UserRole::SUPER_ADMIN);

        $this->actingAs($this->user)
            ->get('_test/tenant-select')
            ->assertSessionMissing(['tenant_id', 'super_admin_tenant_ipa_code']);
    }

    public function testSuperUserTenantEnforced(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $this->user->assign(UserRole::SUPER_ADMIN);

        $this->actingAs($this->user)
            ->get('_test/tenant-select/' . $publicAdministration->ipa_code)
            ->assertSessionHas('super_admin_tenant_ipa_code', $publicAdministration->ipa_code)
            ->assertSessionMissing('tenant_id');
    }
}
