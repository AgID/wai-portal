<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Symfony\Component\Yaml\Yaml;
use Tests\TestCase;

class AnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $publicAdministration;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
        $this->user = factory(User::class)->create([
            'email_verified_at' => Date::now(),
        ]);
        $this->publicAdministration = factory(PublicAdministration::class)->create();
        $this->publicAdministration->users()->sync($this->user->id);

        Bouncer::dontCache();
    }

    public function testPendingUserRedirect(): void
    {
        $user = factory(User::class)->create([
            'email_verified_at' => Date::now(),
        ]);
        $this->actingAs($user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
            ])
            ->get(route('analytics'))
            ->assertRedirect(route('websites.index'));
    }

    public function testUserEmptyPublicAdministrationDashboard(): void
    {
        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
            ])
            ->get(route('analytics'))
            ->assertViewIs('pages.analytics')
            ->assertViewHasAll([
                'publicAdministration' => $this->publicAdministration,
                'widgets' => [],
            ]);
    }

    public function testUserPublicAdministrationDashboard(): void
    {
        $this->publicAdministration->rollup_id = 1;
        $this->publicAdministration->save();

        $widgets = Yaml::parseFile(resource_path('data/widgets.yml'))['pa'];

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
            ])
            ->get(route('analytics'))
            ->assertViewIs('pages.analytics')
            ->assertViewHasAll([
                'publicAdministration' => $this->publicAdministration,
                'widgets' => $widgets,
            ]);
    }

    public function testSuperAdminEmptyPublicAdministrationDashboard(): void
    {
        $this->user->assign(UserRole::SUPER_ADMIN);
        $this->user->allow(UserPermission::ACCESS_ADMIN_AREA);

        $this->actingAs($this->user)
            ->get(route('admin.publicAdministration.analytics', ['publicAdministration' => $this->publicAdministration->ipa_code]))
            ->assertViewIs('pages.analytics')
            ->assertViewHasAll([
                'publicAdministration' => $this->publicAdministration,
                'widgets' => [],
            ]);
    }

    public function testSuperAdminPublicAdministrationDashboard(): void
    {
        $this->publicAdministration->rollup_id = 1;
        $this->publicAdministration->save();
        $this->user->assign(UserRole::SUPER_ADMIN);
        $this->user->allow(UserPermission::ACCESS_ADMIN_AREA);
        $widgets = Yaml::parseFile(resource_path('data/widgets.yml'))['pa'];

        $this->actingAs($this->user)
            ->get(route('admin.publicAdministration.analytics', ['publicAdministration' => $this->publicAdministration->ipa_code]))
            ->assertViewIs('pages.analytics')
            ->assertViewHasAll([
                'publicAdministration' => $this->publicAdministration,
                'widgets' => $widgets,
            ]);
    }
}
