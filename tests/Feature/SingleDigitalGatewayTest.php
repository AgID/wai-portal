<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use App\Traits\ParseUrls;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Storage;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/*
 * Super admin password management test.
 */
class SingleDigitalGatewayTest extends TestCase
{
    use RefreshDatabase;
    use ParseUrls;

    /*
     * Super admin user.
     *
     * @var User the user
     */
    private $user;

    /*
     * Test websites.
     *
     * @var Websites used for testing
     */
    private $websites;

    /*
     * Pre-test setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => Date::now(),
        ]);
        $this->websites = [];

        Bouncer::dontCache();
        Bouncer::scope()->onceTo(0, function () {
            $this->user->assign(UserRole::SUPER_ADMIN);
            $this->user->allow(UserPermission::MANAGE_USERS);
            $this->user->allow(UserPermission::ACCESS_ADMIN_AREA);
        });

        $faker = Factory::create();
        $analyticsService = $this->app->make('analytics-service');

        $pageUrls = [];

        for ($i = 0; $i < rand(5, 10); ++$i) {
            $pageUrl = $faker->url;
            $websiteId = $analyticsService->registerSite($faker->catchPhrase, $this->getFqdnFromUrl($pageUrl), 'test_public_administration');

            array_push($this->websites, $websiteId);
            array_push($pageUrls, $pageUrl);
        }

        Storage::fake('persistent');
        Storage::disk('persistent')->put('sdg/urls.csv', implode("\n", $pageUrls));

        config(['single-digital-gateway-service.url_column_index_csv' => 0]);
    }

    /*
     * Post-test cleanup.
     */
    protected function tearDown(): void
    {
        $analyticsService = $this->app->make('analytics-service');

        Storage::fake('persistent');

        foreach ($this->websites as $websiteId) {
            $analyticsService->deleteSite($websiteId);
        }
    }

    /*
     * Test payload generation.
     */
    public function testDatasetBuild(): void
    {
        config(['analytics-service.cron_archiving_enabled' => false]);

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show'))
            ->assertJson([
                'nbEntries' => count($this->websites),
            ]);

        config(['analytics-service.cron_archiving_enabled' => true]);

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show'))
            ->assertJson([
                'nbEntries' => 0,
            ]);
    }
}
