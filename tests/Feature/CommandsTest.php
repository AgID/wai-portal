<?php

namespace Tests\Unit;

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Ehann\RediSearch\Index;
use Ehann\RedisRaw\PredisAdapter;
use Exception;
use GuzzleHttp\Client as TrackingClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Custom application commands tests.
 */
class CommandsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test check pending website command.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to Analytics Service
     * @throws \GuzzleHttp\Exception\GuzzleException if unable to inject tracking request
     * @throws \App\Exceptions\CommandErrorException if Analytics Service command finishes with error
     */
    public function testCheckPendingWebsites(): void
    {
        $tokenAuth = config('analytics-service.admin_token');

        $user = factory(User::class)->states('pending')->create();
        $userPending = factory(User::class)->states('pending')->create();

        $publicAdministration = factory(PublicAdministration::class)->create();
        $publicAdministrationPending = factory(PublicAdministration::class)->create();

        $user->publicAdministrations()->attach($publicAdministration->id);
        $userPending->publicAdministrations()->attach($publicAdministrationPending->id);
        $user->save();
        $userPending->save();

        $website = factory(Website::class)->make();
        do {
            $websitePending = factory(Website::class)->make();
        } while ($website->slug === $websitePending->slug);

        $publicAdministration->websites()->save($website);
        $publicAdministrationPending->websites()->save($websitePending);
        $publicAdministration->save();
        $publicAdministrationPending->save();

        $analyticsId = $this->app->make('analytics-service')->registerSite('Sito istituzionale', $website->url, $publicAdministration->name);
        $website->analytics_id = $analyticsId;
        $website->save();

        $analyticsPendingId = $this->app->make('analytics-service')->registerSite('Sito istituzionale', $websitePending->url, $publicAdministration->name);
        $websitePending->analytics_id = $analyticsPendingId;
        $websitePending->save();

        $this->app->make('analytics-service')->registerUser($user->uuid, $user->analytics_password, $user->email, $tokenAuth);
        $this->app->make('analytics-service')->registerUser($userPending->uuid, $userPending->analytics_password, $userPending->email, $tokenAuth);

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

        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $analyticsId,
            ],
            'verify' => false,
        ]);

        $websitePending->created_at = now()->subDays((int) config('wai.purge_expiry') + 1);
        $websitePending->save();

        $this->artisan('app:check-websites');

        $this->app->make('analytics-service')->deleteUser($user->uuid, $tokenAuth);

        $this->app->make('analytics-service')->deleteSite($website->analytics_id, $tokenAuth);
        //NOTE: Check pending website job deleted pending website

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
