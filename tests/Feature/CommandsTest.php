<?php

namespace Tests\Unit;

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Ehann\RediSearch\Index;
use Ehann\RedisRaw\PhpRedisAdapter;
use Exception;
use GuzzleHttp\Client as TrackingClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommandsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Models required by this test.
     */
    protected $user;
    protected $user_pending;
    protected $publicAdministration;
    protected $publicAdministration_pending;
    protected $website;
    protected $website_pending;

    /**
     * Test setUp.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->states('pending')->create();
        $this->user_pending = factory(User::class)->states('pending')->create();
        $this->publicAdministration = factory(PublicAdministration::class)->create();
        $this->publicAdministration_pending = factory(PublicAdministration::class)->create();
        $this->user->publicAdministrations()->attach($this->publicAdministration->id);
        $this->user_pending->publicAdministrations()->attach($this->publicAdministration_pending->id);
        $this->user->save();
        $this->user_pending->save();
        $this->website = factory(Website::class)->make();
        $this->website_pending = factory(Website::class)->make();
        $this->publicAdministration->websites()->save($this->website);
        $this->publicAdministration_pending->websites()->save($this->website_pending);
        $this->publicAdministration->save();
        $this->publicAdministration_pending->save();
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
     * Test CheckPendingWebsite class.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function testCheckPendingWebsites()
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

        $this->website_pending->created_at = now()->subDays(16);
        $this->website_pending->save();

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
     * Test CreateRoles class.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function testCreateRoles()
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
     * Test UpdateIPAList class.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function testUpdateIPAList()
    {
        $user = factory(User::class)->states('active')->create();
        $IPAIndex = new Index((new PhpRedisAdapter())->connect(config('database.redis.ipaindex.host'), config('database.redis.ipaindex.port'), config('database.redis.ipaindex.database')), 'IPAIndex');
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
