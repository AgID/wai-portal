<?php

namespace Tests\Unit;

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Exceptions\AnalyticsServiceException;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Ehann\RediSearch\Index;
use Ehann\RedisRaw\PredisAdapter;
use Exception;
use GuzzleHttp\Client as TrackingClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Custom application commands tests.
 */
class CommandsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Models required by this test.
     */
    protected $user;
    protected $userPending;
    protected $publicAdministration;
    protected $publicAdministrationPending;
    protected $website;
    protected $websitePending;

    /**
     * Test setUp.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->states('pending')->create();
        $this->userPending = factory(User::class)->states('pending')->create();
        $this->publicAdministration = factory(PublicAdministration::class)->create();
        $this->publicAdministrationPending = factory(PublicAdministration::class)->create();
        $this->user->publicAdministrations()->attach($this->publicAdministration->id);
        $this->userPending->publicAdministrations()->attach($this->publicAdministrationPending->id);
        $this->user->save();
        $this->userPending->save();
        $this->website = factory(Website::class)->make();
        $this->websitePending = factory(Website::class)->make();
        $this->publicAdministration->websites()->save($this->website);
        $this->publicAdministrationPending->websites()->save($this->websitePending);
        $this->publicAdministration->save();
        $this->publicAdministrationPending->save();
    }

    /**
     * Test tearDown.
     */
    protected function tearDown(): void
    {
        if (isset($this->website->analytics_id)) {
            $this->app->make('analytics-service')->deleteSite($this->website->analytics_id);
            $this->app->make('analytics-service')->deleteUser($this->user->email);
        }
    }

    /**
     * Test check pending website command.
     *
     * @throws AnalyticsServiceException
     * @throws BindingResolutionException
     * @throws GuzzleException
     */
    public function testCheckPendingWebsites(): void
    {
        $this->artisan('app:check-websites');

        $this->assertDatabaseHas('users', [
            'status' => UserStatus::PENDING,
        ]);
        $this->assertDatabaseHas('public_administrations', [
            'status' => PublicAdministrationStatus::PENDING,
        ]);
        $this->assertDatabaseHas('websites', [
            'status' => WebsiteStatus::PENDING,
        ]);

        $analyticsId = $this->app->make('analytics-service')->registerSite('Sito istituzionale', $this->website->url, $this->publicAdministration->name);
        $this->website->analytics_id = $analyticsId;
        $this->website->save();

        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', '/piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $analyticsId,
            ],
            'verify' => false,
        ]);

        $this->websitePending->created_at = now()->subDays(16);
        $this->websitePending->save();

        $this->artisan('app:check-websites');

        $this->assertDatabaseHas('websites', [
            'status' => WebsiteStatus::ACTIVE,
        ]);
        $this->assertDatabaseHas('public_administrations', [
            'status' => PublicAdministrationStatus::ACTIVE,
        ]);
        $this->assertDatabaseHas('websites', [
            'status' => WebsiteStatus::ACTIVE,
        ]);
        $this->assertDatabaseMissing('public_administrations', [
            'status' => PublicAdministrationStatus::PENDING,
        ]);
        $this->assertDatabaseMissing('websites', [
            'status' => WebsiteStatus::PENDING,
        ]);
    }

    /**
     * Test create roles command.
     */
    public function testCreateRoles(): void
    {
        $this->assertDatabaseMissing('roles', [
            'name' => 'registered',
            'name' => 'reader',
            'name' => 'manager',
            'name' => 'admin',
            'name' => 'super-admin',
        ]);
        $this->artisan('app:create-roles');
        $this->assertDatabaseHas('roles', [
            'name' => 'registered',
            'name' => 'reader',
            'name' => 'manager',
            'name' => 'admin',
            'name' => 'super-admin',
        ]);
    }

    /**
     * Test update IPA command.
     */
    public function testUpdateIPAList(): void
    {
        $user = factory(User::class)->states('active')->create();
        $IPAIndex = new Index((new PredisAdapter())->connect(config('database.redis.ipaindex.host'), config('database.redis.ipaindex.port'), config('database.redis.ipaindex.database')), 'IPAIndex');
        try {
            $IPAIndex->drop();
        } catch (Exception $e) {
            // Index already dropped, it's ok!
        }
        $response = $this->actingAs($user)
            ->withSession(['spid_sessionIndex' => 'fake-session-index'])
            ->post(route('search-ipa-list'), ['q' => 'camera']);
        $response->assertJson([]);
        $this->artisan('app:update-ipa');
        $response = $this->actingAs($user)
            ->withSession(['spid_sessionIndex' => 'fake-session-index'])
            ->post(route('search-ipa-list'), ['q' => 'camera']);
        $response->assertJson([['id' => 'camera']]);
    }
}
