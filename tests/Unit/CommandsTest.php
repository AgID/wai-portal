<?php

namespace Tests\Unit;

use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Ehann\RediSearch\Index;
use Ehann\RediSearch\Redis\RedisClient;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use GuzzleHttp\Client as TrackingClient;
use Tests\TestCase;

class CommandsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test CheckPendingWebsite class
     *
     * @return void
     * @throws \Exception
     */
    public function testCheckPendingWebsites()
    {
        $this->assertDatabaseMissing('users', [
            'status' => 'pending'
        ]);
        $this->assertDatabaseMissing('public_administrations', [
            'status' => 'pending'
        ]);
        $this->assertDatabaseMissing('websites', [
            'status' => 'pending'
        ]);
        $user = factory(User::class)->states('pending')->create();
        $user->publicAdministration()->associate(factory(PublicAdministration::class)->create());
        $user->save();
        $website = factory(Website::class)->make();
        $user->publicAdministration->websites()->save($website);
        $user->publicAdministration->save();
        $this->artisan('app:check-websites');
        $this->assertDatabaseHas('users', [
            'status' => 'pending'
        ]);
        $this->assertDatabaseHas('public_administrations', [
            'status' => 'pending'
        ]);
        $this->assertDatabaseHas('websites', [
            'status' => 'pending'
        ]);
        $analyticsId = $this->app->make('analytics-service')->registerSite('Sito istituzionale', $website->url, $user->publicAdministration->name);
        $website->analytics_id = $analyticsId;
        $website->save();
        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', '/piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $analyticsId
            ],
            'verify' => false
        ]);
        $this->artisan('app:check-websites');
        $this->assertDatabaseHas('websites', [
            'status' => 'active'
        ]);
        $this->assertDatabaseHas('public_administrations', [
            'status' => 'active'
        ]);
        $this->assertDatabaseHas('websites', [
            'status' => 'active'
        ]);
        $this->app->make('analytics-service')->deleteSite($analyticsId);
        $this->app->make('analytics-service')->deleteUser($user->email);
    }

    /**
     * Test CreateRoles class
     *
     * @return void
     * @throws \Exception
     */
    public function testCreateRoles()
    {
        $this->assertDatabaseMissing('roles', [
            'name' => 'registered',
            'name' => 'reader',
            'name' => 'manager',
            'name' => 'admin',
            'name' => 'super-admin'
        ]);
        $this->artisan('app:create-roles');
        $this->assertDatabaseHas('roles', [
            'name' => 'registered',
            'name' => 'reader',
            'name' => 'manager',
            'name' => 'admin',
            'name' => 'super-admin'
        ]);
    }

    /**
     * Test UpdateIPAList class
     *
     * @return void
     * @throws \Exception
     */
    public function testUpdateIPAList()
    {
        $user = factory(User::class)->states('active')->create();
        $clientClassName = config('database.redis.client') == 'phpredis' ? 'Redis' : 'Predis\Client';
        $IPAIndex = new Index(new RedisClient($clientClassName, config('database.redis.ipaindex.host'), config('database.redis.ipaindex.port'), config('database.redis.ipaindex.database')), 'IPAIndex');
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
        $response->assertJson([['ipa_code' => 'camera']]);
    }
}
