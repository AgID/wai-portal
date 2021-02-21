<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Exceptions\SDGServiceException;
use App\Models\User;
use App\Traits\ParseUrls;
use Carbon\Carbon;
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
     * Test env storageDisk.
     *
     * @var storage disk used for testing
     */
    private $storageDisk;

    /*
     * Test env storageDirectory.
     *
     * @var storage directory used for testing
     */
    private $storageDirectory;

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

        $jsonArray = array_map(function ($url) {
            return ['test_url' => $url];
        }, $pageUrls);

        $this->storageDisk = config('single-digital-gateway-service.storage_disk');
        $this->storageDirectory = config('single-digital-gateway-service.storage_directory');

        Storage::fake($this->storageDisk);
        Storage::disk($this->storageDisk)->put($this->storageDirectory . '/urls.csv', implode("\n", $pageUrls));
        Storage::disk($this->storageDisk)->put($this->storageDirectory . '/urls.json', json_encode(['test_path' => $jsonArray], JSON_PRETTY_PRINT));

        $this->withoutExceptionHandling();
    }

    /*
     * Post-test cleanup.
     */
    protected function tearDown(): void
    {
        $analyticsService = $this->app->make('analytics-service');

        Storage::fake($this->storageDisk);

        foreach ($this->websites as $websiteId) {
            $analyticsService->deleteSite($websiteId);
        }

        $segments = $analyticsService->getAllSegments();
        foreach ($segments as $segment) {
            $analyticsService->deleteSegment($segment['idsegment']);
        }
    }

    /*
     * Test payload generation from CSV data file.
     */
    public function testDatasetBuildCsv(): void
    {
        config(['single-digital-gateway-service.urls_file_format' => 'csv']);
        config(['single-digital-gateway-service.url_column_index_csv' => 0]);
        config(['analytics-service.cron_archiving_enabled' => false]);

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show'))
            ->assertJson([
                'nbEntries' => count($this->websites),
            ]);
    }

    /*
     * Test payload generation from CSV data file.
     */
    public function testDatasetBuildCsvWithCronArchivingEnabled(): void
    {
        config(['single-digital-gateway-service.urls_file_format' => 'csv']);
        config(['single-digital-gateway-service.url_column_index_csv' => 0]);
        config(['analytics-service.cron_archiving_enabled' => true]);

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show'))
            ->assertJson([
                'nbEntries' => 0,
            ]);
    }

    /*
     * Test payload generation failure from missing CSV data file.
     */
    public function testDatasetBuildFailMissingCsvFile(): void
    {
        config(['single-digital-gateway-service.urls_file_format' => 'csv']);

        Storage::disk($this->storageDisk)->delete($this->storageDirectory . '/urls.csv');

        $this->expectException(SDGServiceException::class);

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show'));
    }

    /*
     * Test payload generation from JSON data file.
     */
    public function testDatasetBuildJson(): void
    {
        config(['single-digital-gateway-service.urls_file_format' => 'json']);
        config(['single-digital-gateway-service.url_array_path_json' => 'test_path']);
        config(['single-digital-gateway-service.url_key_json' => 'test_url']);
        config(['analytics-service.cron_archiving_enabled' => false]);

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show'))
            ->assertJson([
                'nbEntries' => count($this->websites),
            ]);
    }

    /*
     * Test payload generation from JSON data file.
     */
    public function testDatasetBuildJsonWithCronArchivingEnabled(): void
    {
        config(['single-digital-gateway-service.urls_file_format' => 'json']);
        config(['single-digital-gateway-service.url_array_path_json' => 'test_path']);
        config(['single-digital-gateway-service.url_key_json' => 'test_url']);
        config(['analytics-service.cron_archiving_enabled' => true]);

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show'))
            ->assertJson([
                'nbEntries' => 0,
            ]);
    }

    /*
     * Test payload generation failure from missing JSON data file.
     */
    public function testDatasetBuildFailMissingJsonFile(): void
    {
        config(['single-digital-gateway-service.urls_file_format' => 'json']);

        Storage::disk($this->storageDisk)->delete($this->storageDirectory . '/urls.json');

        $this->expectException(SDGServiceException::class);

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show'));
    }

    /*
     * Test payload generation failure with wrong JSON path.
     */
    public function testDatasetBuildFailWithJsonWrongPath(): void
    {
        config(['single-digital-gateway-service.urls_file_format' => 'json']);
        config(['single-digital-gateway-service.url_array_path_json' => 'wrong_test_path']);

        $this->expectException(SDGServiceException::class);

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show'));
    }

    /*
     * Test empty payload generation with wrong url key for JSON data file.
     */
    public function testEmptyDatasetBuildFailWithWrongJsonKey(): void
    {
        config(['single-digital-gateway-service.urls_file_format' => 'json']);
        config(['single-digital-gateway-service.url_array_path_json' => 'test_path']);
        config(['single-digital-gateway-service.url_key_json' => 'wrong_key']);
        config(['analytics-service.cron_archiving_enabled' => false]);

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show'))
            ->assertJson([
                'nbEntries' => 0,
            ]);
    }

    /*
     * Test empty payload generation with wrong url key for JSON data file.
     */
    public function testEmptyDatasetBuildWithMissingJsonKey(): void
    {
        config(['single-digital-gateway-service.urls_file_format' => 'json']);
        config(['single-digital-gateway-service.url_array_path_json' => 'test_path']);
        config(['analytics-service.cron_archiving_enabled' => false]);

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show'))
            ->assertJson([
                'nbEntries' => 0,
            ]);
    }

    /*
     * Test payload generation with a specified period.
     */
    public function testDatasetBuildWithPeriod(): void
    {
        config(['single-digital-gateway-service.urls_file_format' => 'json']);
        config(['single-digital-gateway-service.url_array_path_json' => 'test_path']);
        config(['single-digital-gateway-service.url_key_json' => 'test_url']);
        config(['analytics-service.cron_archiving_enabled' => false]);

        $randomStartDate = Carbon::today('UTC')->subDays(rand(30, 365))->startOfDay();
        $randomEndDate = $randomStartDate->clone()->addDays(30)->endOfDay();
        $randomPeriod = implode(',', [
            $randomStartDate->toDateString(),
            $randomEndDate->toDateString(),
        ]);

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show') . '?period=' . $randomPeriod)
            ->assertJson([
                'nbEntries' => count($this->websites),
                'referencePeriod' => [
                    'startDate' => $randomStartDate->toIso8601ZuluString(),
                    'endDate' => $randomEndDate->toIso8601ZuluString(),
                ],
            ]);
    }

    /*
     * Test payload generation failure with an invalid specified period.
     */
    public function testDatasetBuildFailWithInvalidPeriod(): void
    {
        config(['single-digital-gateway-service.urls_file_format' => 'json']);
        config(['single-digital-gateway-service.url_array_path_json' => 'test_path']);
        config(['single-digital-gateway-service.url_key_json' => 'test_url']);
        config(['analytics-service.cron_archiving_enabled' => false]);

        $this->expectException(SDGServiceException::class);
        $this->expectExceptionMessage('Invalid period parameter');

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show') . '?period=not-valid');
    }

    /*
     * Test payload generation failure with an invalid date in the specified period.
     */
    public function testDatasetBuildFailWithInvalidDateInPeriod(): void
    {
        config(['single-digital-gateway-service.urls_file_format' => 'json']);
        config(['single-digital-gateway-service.url_array_path_json' => 'test_path']);
        config(['single-digital-gateway-service.url_key_json' => 'test_url']);
        config(['analytics-service.cron_archiving_enabled' => false]);

        $this->expectException(SDGServiceException::class);
        $this->expectExceptionMessage('Invalid date in period parameter');

        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show') . '?period=2021-01-01,2021-01-40');
    }
}
