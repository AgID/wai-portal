<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Jobs\ProcessWebsitesList;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class SearchWebsiteListRouteTest extends TestCase
{
    use RefreshDatabase;

    private $superAdmin;

    private $firstUser;

    private $secondUser;

    private $firstPublicAdministration;

    private $secondPublicAdministration;

    private $firstWebsite;

    private $secondWebsite;

    public function setUp(): void
    {
        parent::setUp();
        Bouncer::dontCache();

        $this->superAdmin = factory(User::class)->create([
            'email_verified_at' => Carbon::now(),
        ]);
        Bouncer::scope()->to(0);
        $this->superAdmin->assign(UserRole::SUPER_ADMIN);
        $this->superAdmin->allow(UserPermission::ACCESS_ADMIN_AREA);

        $this->firstUser = factory(User::class)->create([
            'email_verified_at' => Carbon::now(),
        ]);
        $this->secondUser = factory(User::class)->create([
            'email_verified_at' => Carbon::now(),
        ]);
        $this->firstPublicAdministration = factory(PublicAdministration::class)->create();
        $this->secondPublicAdministration = factory(PublicAdministration::class)->create();
        $this->firstUser->publicAdministrations()->sync($this->firstPublicAdministration->id);
        $this->secondUser->publicAdministrations()->sync($this->secondPublicAdministration->id);

        Bouncer::scope()->to($this->firstPublicAdministration->id);
        $this->firstUser->assign(UserRole::ADMIN);
        $this->firstUser->allow(UserPermission::VIEW_LOGS);

        Bouncer::scope()->to($this->secondPublicAdministration->id);
        $this->secondUser->assign(UserRole::ADMIN);
        $this->secondUser->allow(UserPermission::VIEW_LOGS);

        $this->firstWebsite = factory(Website::class)->create([
            'slug' => Str::slug('www.sito1.it'),
            'public_administration_id' => $this->firstPublicAdministration->id,
        ]);

        $this->secondWebsite = factory(Website::class)->create([
            'slug' => Str::slug('www.sito2.it'),
            'public_administration_id' => $this->secondPublicAdministration->id,
        ]);

        (new ProcessWebsitesList())->handle();
    }

    public function testSuperAdminSearch(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')
            ->post(route('admin.logs.search-website'), ['q' => 'www', 'p' => null]);

        $response->assertJsonFragment(
            [
                'id' => $this->firstWebsite->slug,
                'pa' => $this->firstPublicAdministration->ipa_code,
                'slug' => $this->firstWebsite->slug,
                'name' => $this->firstWebsite->name,
            ]
        );
        $response->assertJsonFragment(
            [
                'id' => $this->secondWebsite->slug,
                'pa' => $this->secondPublicAdministration->ipa_code,
                'slug' => $this->secondWebsite->slug,
                'name' => $this->secondWebsite->name,
            ]
        );
    }

    public function testFirstAdminsSearch(): void
    {
        $response = $this->actingAs($this->firstUser, 'web')
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->firstPublicAdministration->id,
            ])
            ->post(route('logs.search-website'), ['q' => 'www']);

        $response->assertExactJson([
            [
                'id' => $this->firstWebsite->slug,
                'pa' => $this->firstPublicAdministration->ipa_code,
                'slug' => $this->firstWebsite->slug,
                'name' => $this->firstWebsite->name,
            ],
        ]);

        $response->assertDontSee(json_encode(
            [
                'id' => $this->secondWebsite->slug,
                'pa' => $this->secondPublicAdministration->ipa_code,
                'slug' => $this->secondWebsite->slug,
                'name' => $this->secondWebsite->name,
            ]
        ));
    }

    public function testSecondAdminsSearch(): void
    {
        $response = $this->actingAs($this->secondUser, 'web')
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->secondPublicAdministration->id,
            ])
            ->post(route('logs.search-website'), ['q' => 'www']);

        $response->assertExactJson([
            [
                'id' => $this->secondWebsite->slug,
                'pa' => $this->secondPublicAdministration->ipa_code,
                'slug' => $this->secondWebsite->slug,
                'name' => $this->secondWebsite->name,
            ],
        ]);

        $response->assertDontSee(json_encode(
            [
                'id' => $this->firstWebsite->slug,
                'pa' => $this->firstPublicAdministration->ipa_code,
                'slug' => $this->firstWebsite->slug,
                'name' => $this->firstWebsite->name,
            ]
        ));
    }

    public function testIPACodeFilteringOnSuperAdmin(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')
            ->post(route('admin.logs.search-website'), ['q' => 'www', 'p' => $this->firstPublicAdministration->ipa_code]);

        $response->assertExactJson([
            [
                'id' => $this->firstWebsite->slug,
                'pa' => $this->firstPublicAdministration->ipa_code,
                'slug' => $this->firstWebsite->slug,
                'name' => $this->firstWebsite->name,
            ],
        ]);

        $response->assertDontSee(json_encode(
            [
                'id' => $this->secondWebsite->slug,
                'pa' => $this->secondPublicAdministration->ipa_code,
                'slug' => $this->secondWebsite->slug,
                'name' => $this->secondWebsite->name,
            ]
        ));
    }

    public function testIPACodeFilteringOnAdmin(): void
    {
        $response = $this->actingAs($this->firstUser, 'web')
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->firstPublicAdministration->id,
            ])
            ->post(route('logs.search-website'), ['q' => 'www', 'p' => $this->secondPublicAdministration->ipa_code]);

        $response->assertExactJson([
            [
                'id' => $this->firstWebsite->slug,
                'pa' => $this->firstPublicAdministration->ipa_code,
                'slug' => $this->firstWebsite->slug,
                'name' => $this->firstWebsite->name,
            ],
        ]);

        $response->assertDontSee(json_encode(
            [
                'id' => $this->secondWebsite->slug,
                'pa' => $this->secondPublicAdministration->ipa_code,
                'slug' => $this->secondWebsite->slug,
                'name' => $this->secondWebsite->name,
            ]
        ));
    }
}
