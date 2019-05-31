<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Jobs\ProcessUsersList;
use App\Models\PublicAdministration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/**
 * Users index controller test.
 */
class SearchUserListRouteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The super-admin user.
     *
     * @var User super-admin user
     */
    private $superAdmin;

    /**
     * The first user.
     *
     * @var User a user
     */
    private $firstUser;

    /**
     * The second user.
     *
     * @var User another user
     */
    private $secondUser;

    /**
     * The first public administration.
     *
     * @var PublicAdministration a public administration
     */
    private $firstPublicAdministration;

    /**
     * The second public administration.
     *
     * @var PublicAdministration a public administration
     */
    private $secondPublicAdministration;

    /**
     * Pre-test setup.
     */
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
            'name' => 'Mario',
            'email_verified_at' => Carbon::now(),
        ]);
        $this->secondUser = factory(User::class)->create([
            'name' => 'Mario',
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

        (new ProcessUsersList())->handle();
    }

    /**
     * Test super-admin user search capabilities.
     */
    public function testSuperAdminSearch(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')
            ->post(route('admin.logs.search-user'), ['q' => 'Mario', 'p' => null]);

        $response->assertJsonFragment(
            [
                'id' => $this->firstUser->uuid,
                'pas' => $this->firstPublicAdministration->ipa_code,
                'uuid' => $this->firstUser->uuid,
                'familyName' => $this->firstUser->familyName,
                'name' => $this->firstUser->name,
            ]
        );
        $response->assertJsonFragment(
            [
                'id' => $this->secondUser->uuid,
                'pas' => $this->secondPublicAdministration->ipa_code,
                'uuid' => $this->secondUser->uuid,
                'familyName' => $this->secondUser->familyName,
                'name' => $this->secondUser->name,
            ]
        );
    }

    /**
     * Test first user search capabilities.
     */
    public function testPublicAdministrationFirstAdminSearch(): void
    {
        $response = $this->actingAs($this->firstUser, 'web')
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->firstPublicAdministration->id,
            ])
            ->post(route('logs.search-user'), ['q' => 'Mario']);

        $response->assertExactJson([
            [
                'id' => $this->firstUser->uuid,
                'pas' => $this->firstPublicAdministration->ipa_code,
                'uuid' => $this->firstUser->uuid,
                'familyName' => $this->firstUser->familyName,
                'name' => $this->firstUser->name,
            ],
        ]);

        $response->assertDontSee(json_encode(
            [
                'id' => $this->secondUser->uuid,
                'pas' => $this->secondPublicAdministration->ipa_code,
                'uuid' => $this->secondUser->uuid,
                'familyName' => $this->secondUser->familyName,
                'name' => $this->secondUser->name,
            ]
        ));
    }

    /**
     * Test second user search capabilities.
     */
    public function testPublicAdministrationSecondAdminSearch(): void
    {
        $response = $this->actingAs($this->secondUser, 'web')
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->secondPublicAdministration->id,
            ])
            ->post(route('logs.search-user'), ['q' => 'Mario']);

        $response->assertExactJson([
            [
                'id' => $this->secondUser->uuid,
                'pas' => $this->secondPublicAdministration->ipa_code,
                'uuid' => $this->secondUser->uuid,
                'familyName' => $this->secondUser->familyName,
                'name' => $this->secondUser->name,
            ],
        ]);

        $response->assertDontSee(json_encode(
            [
                'id' => $this->firstUser->uuid,
                'pas' => $this->firstPublicAdministration->ipa_code,
                'uuid' => $this->firstUser->uuid,
                'familyName' => $this->firstUser->familyName,
                'name' => $this->firstUser->name,
            ]
        ));
    }

    /**
     * Test super-admin user search capabilities using an I.P.A. code.
     */
    public function testIPACodeFilteringOnSuperAdmin(): void
    {
        $response = $this->actingAs($this->superAdmin, 'web')
            ->post(route('admin.logs.search-user'), ['q' => 'Mario', 'p' => $this->firstPublicAdministration->ipa_code]);

        $response->assertExactJson([
            [
                'id' => $this->firstUser->uuid,
                'pas' => $this->firstPublicAdministration->ipa_code,
                'uuid' => $this->firstUser->uuid,
                'familyName' => $this->firstUser->familyName,
                'name' => $this->firstUser->name,
            ],
        ]);

        $response->assertDontSee(json_encode(
            [
                'id' => $this->secondUser->uuid,
                'pas' => $this->secondPublicAdministration->ipa_code,
                'uuid' => $this->secondUser->uuid,
                'familyName' => $this->secondUser->familyName,
                'name' => $this->secondUser->name,
            ]
        ));
    }

    /**
     * Test first user search capabilities using an I.P.A. code.
     */
    public function testIPACodeFilteringOnAdmin(): void
    {
        $response = $this->actingAs($this->firstUser, 'web')
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->firstPublicAdministration->id,
            ])
            ->post(route('logs.search-user'), ['q' => 'Mario', 'p' => $this->secondPublicAdministration->ipa_code]);

        $response->assertExactJson([
            [
                'id' => $this->firstUser->uuid,
                'pas' => $this->firstPublicAdministration->ipa_code,
                'uuid' => $this->firstUser->uuid,
                'familyName' => $this->firstUser->familyName,
                'name' => $this->firstUser->name,
            ],
        ]);

        $response->assertDontSee(json_encode(
            [
                'id' => $this->secondUser->uuid,
                'pas' => $this->secondPublicAdministration->ipa_code,
                'uuid' => $this->secondUser->uuid,
                'familyName' => $this->secondUser->familyName,
                'name' => $this->secondUser->name,
            ]
        ));
    }
}
