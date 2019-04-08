<?php

namespace Tests\Feature;

use App\Enums\WebsiteAccessType;
use App\Enums\WebsiteStatus;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use GuzzleHttp\Client as TrackingClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class JsonRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected $userTokenAuth;

    protected $publicAdministration;

    protected $website;

    protected function setUp(): void
    {
        parent::setUp();
        $tokenAuth = config('analytics-service.admin_token');
        $this->user = factory(User::class)->state('active')->create();
        $this->publicAdministration = factory(PublicAdministration::class)->create();
        $this->publicAdministration->users()->sync($this->user->id);
        $this->website = factory(Website::class)->create([
            'status' => WebsiteStatus::PENDING,
            'public_administration_id' => $this->publicAdministration->id,
        ]);

        $analyticsID = $this->app->make('analytics-service')->registerSite($this->website->name, $this->website->url, $this->publicAdministration->name);
        $this->website->analytics_id = $analyticsID;
        $this->website->save();
        $this->app->make('analytics-service')->registerUser($this->user->uuid, $this->user->analytics_password, $this->user->email, $tokenAuth, $this->user->full_name);
        $this->app->make('analytics-service')->setWebsitesAccess($this->user->uuid, WebsiteAccessType::VIEW, $this->website->analytics_id, $tokenAuth);
        $this->userTokenAuth = $this->app->make('analytics-service')->getUserAuthToken($this->user->uuid, md5($this->user->analytics_password));

        Bouncer::scope()->to($this->publicAdministration->id);
        $this->user->assign('reader');
        $this->user->allow('read-analytics');
    }

    protected function tearDown(): void
    {
        $tokenAuth = config('analytics-service.admin_token');
        $this->app->make('analytics-service')->deleteUser($this->user->uuid, $tokenAuth);
        $this->app->make('analytics-service')->deleteSite($this->website->analytics_id, $tokenAuth);
        parent::tearDown();
    }

    public function testCheckWebsiteNotActiveRoute()
    {
        $response = $this->actingAs($this->user, 'web')
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
            ])
            ->get(route('website-check_tracking', ['website' => $this->website->slug]));

        $response->assertStatus(200);

        $response->assertJson([
            'result' => 'ok',
            'id' => $this->website->slug,
            'status' => WebsiteStatus::getDescription(WebsiteStatus::PENDING),
        ]);
    }

    public function testCheckWebsiteActiveRoute()
    {
        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $this->website->analytics_id,
            ],
            'verify' => false,
        ]);

        $response = $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
            ])
            ->get(route('website-check_tracking', ['website' => $this->website->slug]));

        $response->assertJson([
            'result' => 'ok',
            'id' => $this->website->slug,
            'status' => WebsiteStatus::getDescription(WebsiteStatus::ACTIVE),
        ]);
    }

    public function testCheckWebsiteFailedRoute()
    {
        $website = factory(Website::class)->create([
            'public_administration_id' => $this->publicAdministration->id,
            'status' => WebsiteStatus::PENDING,
        ]);

        $response = $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
            ])
            ->get(route('website-check_tracking', ['website' => $website->slug]));

        $response->assertJson([
            'result' => 'error',
            'message' => 'Bad Request',
        ]);
    }
}
