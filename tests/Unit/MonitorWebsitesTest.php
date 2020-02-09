<?php

namespace Tests\Unit;

use App\Enums\WebsiteType;
use App\Events\Jobs\WebsitesMonitoringCheckCompleted;
use App\Jobs\MonitorWebsitesTracking;
use App\Models\PublicAdministration;
use App\Models\Website;
use Carbon\Carbon;
use GuzzleHttp\Client as TrackingClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Websites activity check job tests.
 */
class MonitorWebsitesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    /**
     * Test job completed successfully.
     */
    public function testMonitorCheckCompleted(): void
    {
        $job = new MonitorWebsitesTracking();
        $job->handle();

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class);
    }

    /**
     * Test job complete with scheduled to archive website.
     * NOTE: in this test a notification should be sent because the site has less than 'wai.archive_warning_daily_notification'
     *       days left, so system sends daily notification.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \GuzzleHttp\Exception\GuzzleException if unable to inject tracking request
     */
    public function testMonitorWebsiteArchivingForDailyNotification(): void
    {
        $daysToSub = (int) config('wai.archive_expire') - (int) config('wai.archive_warning_daily_notification');

        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
            'type' => WebsiteType::SECONDARY,
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subDays((int) config('wai.archive_expire') + 1),
        ]);

        $analyticsId = $this->app->make('analytics-service')->registerSite($website->name, $website->url, $publicAdministration->name);

        $website->analytics_id = $analyticsId;
        $website->save();

        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $analyticsId,
                'cdt' => now()->subDays($daysToSub)->timestamp,
                'token_auth' => config('analytics-service.admin_token'),
            ],
            'verify' => false,
        ]);

        $job = new MonitorWebsitesTracking();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id);

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getArchiving(), true)
                && empty($event->getArchived())
                && empty($event->getFailed());
        });
    }

    /**
     * Test job complete with scheduled to archive website.
     * NOTE: in this test a notification should be sent because the site has more than 'wai.archive_warning_daily_notification'
     *       days left but the last visit was 'wai.archive_warning' days ago.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \GuzzleHttp\Exception\GuzzleException if unable to inject tracking request
     */
    public function testMonitorCheckWebsiteArchivingNotificationForWarningThresholdReached(): void
    {
        $daysToSub = (int) config('wai.archive_warning');

        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
            'type' => WebsiteType::SECONDARY,
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subDays((int) config('wai.archive_expire') + 1),
        ]);

        $analyticsId = $this->app->make('analytics-service')->registerSite($website->name, $website->url, $publicAdministration->name);

        $website->analytics_id = $analyticsId;
        $website->save();

        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $analyticsId,
                'cdt' => now()->subDays($daysToSub)->timestamp,
                'token_auth' => config('analytics-service.admin_token'),
            ],
            'verify' => false,
        ]);

        $job = new MonitorWebsitesTracking();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id);

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getArchiving(), true)
                && empty($event->getArchived())
                && empty($event->getFailed());
        });
    }

    /**
     * Test job complete with scheduled to archive website.
     * NOTE: in this test a notification should be sent because the site has more than 'wai.archive_warning_daily_notification'
     *       days left, but the last visit is in 'wai.archive_warning_notification_interval' interval.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \GuzzleHttp\Exception\GuzzleException if unable to inject tracking request
     */
    public function testMonitorCheckWebsiteArchivingForPeriodicNotification(): void
    {
        $daysToSub = (int) config('wai.archive_warning') + (int) config('wai.archive_warning_notification_interval');

        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
            'type' => WebsiteType::SECONDARY,
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subDays((int) config('wai.archive_expire') + 1),
        ]);

        $analyticsId = $this->app->make('analytics-service')->registerSite($website->name, $website->url, $publicAdministration->name);

        $website->analytics_id = $analyticsId;
        $website->save();

        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $analyticsId,
                'cdt' => now()->subDays($daysToSub)->timestamp,
                'token_auth' => config('analytics-service.admin_token'),
            ],
            'verify' => false,
        ]);

        $job = new MonitorWebsitesTracking();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id);

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getArchiving(), true)
                && empty($event->getArchived())
                && empty($event->getFailed());
        });
    }

    /**
     * Test job complete with scheduled without any website reported.
     * NOTE: in this test a notification should not be sent because the site has more
     *       than 'wai.archive_warning_daily_notification' days left and the last visit is not
     *       'wai.archive_warning' days ago nor in 'wai.archive_warning_notification_interval' interval.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \GuzzleHttp\Exception\GuzzleException if unable to inject tracking request
     */
    public function testMonitorCheckWebsiteArchivingNoNotification(): void
    {
        $daysToSub = (int) config('wai.archive_warning') + 1;

        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
            'type' => WebsiteType::SECONDARY,
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subDays((int) config('wai.archive_expire') + 1),
        ]);

        $analyticsId = $this->app->make('analytics-service')->registerSite($website->name, $website->url, $publicAdministration->name);

        $website->analytics_id = $analyticsId;
        $website->save();

        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $analyticsId,
                'cdt' => now()->subDays($daysToSub)->timestamp,
                'token_auth' => config('analytics-service.admin_token'),
            ],
            'verify' => false,
        ]);

        $job = new MonitorWebsitesTracking();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id);

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) {
            return empty($event->getArchiving())
                && empty($event->getArchived())
                && empty($event->getFailed());
        });
    }

    /**
     * Test job complete with an archived website.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \GuzzleHttp\Exception\GuzzleException if unable to inject tracking request
     */
    public function testMonitorCheckArchived(): void
    {
        $daysToSub = (int) config('wai.archive_expire');

        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
            'type' => WebsiteType::SECONDARY,
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subDays($daysToSub),
        ]);
        $analyticsId = $this->app->make('analytics-service')->registerSite($website->name, $website->url, $publicAdministration->name);

        $website->analytics_id = $analyticsId;
        $website->save();

        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $analyticsId,
                'cdt' => now()->subDays($daysToSub)->timestamp,
                'token_auth' => config('analytics-service.admin_token'),
            ],
            'verify' => false,
        ]);

        $job = new MonitorWebsitesTracking();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id);

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getArchived(), true)
                && empty($event->getArchiving())
                && empty($event->getFailed());
        });
    }

    /**
     * Test job complete with an archiving website even after expiration due to primary website.
     * NOTE: in this test a notification should be sent because the last visit is more than
     *       'wai.archive_expire' days ago and it is 'wai.primary_website_not_tracking_notification_day'
     *       notification day.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \GuzzleHttp\Exception\GuzzleException if unable to inject tracking request
     */
    public function testMonitorCheckPrimaryWebsiteArchivingNotificationAfterExpireThreshold(): void
    {
        $daysToSub = (int) config('wai.archive_expire');

        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
            'type' => WebsiteType::PRIMARY,
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subDays($daysToSub),
        ]);
        $analyticsId = $this->app->make('analytics-service')->registerSite($website->name, $website->url, $publicAdministration->name);

        $website->analytics_id = $analyticsId;
        $website->save();

        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $analyticsId,
                'cdt' => now()->subDays($daysToSub)->timestamp,
                'token_auth' => config('analytics-service.admin_token'),
            ],
            'verify' => false,
        ]);

        $date = Carbon::now()->addWeek()->isoWeekday((int) config('wai.primary_website_not_tracking_notification_day'));
        Carbon::setTestNow($date);

        $job = new MonitorWebsitesTracking();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id);

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getArchiving(), true)
                && empty($event->getArchived())
                && empty($event->getFailed());
        });
    }

    /**
     * Test job complete with scheduled to archive website.
     * NOTE: in this test a notification should be sent because the site has more than 'wai.archive_warning_daily_notification'
     *       days left but the last visit was 'wai.archive_warning' days ago.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \GuzzleHttp\Exception\GuzzleException if unable to inject tracking request
     */
    public function testMonitorCheckPrimaryWebsiteArchivingForWarningThresholdReached(): void
    {
        $daysToSub = (int) config('wai.archive_warning');

        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
            'type' => WebsiteType::SECONDARY,
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subDays((int) config('wai.archive_expire') + 1),
        ]);

        $analyticsId = $this->app->make('analytics-service')->registerSite($website->name, $website->url, $publicAdministration->name);

        $website->analytics_id = $analyticsId;
        $website->save();

        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $analyticsId,
                'cdt' => now()->subDays($daysToSub)->timestamp,
                'token_auth' => config('analytics-service.admin_token'),
            ],
            'verify' => false,
        ]);

        $job = new MonitorWebsitesTracking();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id);

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getArchiving(), true)
                && empty($event->getArchived())
                && empty($event->getFailed());
        });
    }

    /**
     * Test job complete with scheduled to archive website.
     * NOTE: in this test a notification should be sent because the site has more than 'wai.archive_warning_daily_notification'
     *       days left, but the last visit is in 'wai.archive_warning_notification_interval' interval.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \GuzzleHttp\Exception\GuzzleException if unable to inject tracking request
     */
    public function testMonitorCheckPrimaryArchivingDuringWarningWindowNoNotification(): void
    {
        $daysToSub = (int) config('wai.archive_warning') + (int) config('wai.archive_warning_notification_interval');

        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
            'type' => WebsiteType::PRIMARY,
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subDays((int) config('wai.archive_expire') + 1),
        ]);

        $analyticsId = $this->app->make('analytics-service')->registerSite($website->name, $website->url, $publicAdministration->name);

        $website->analytics_id = $analyticsId;
        $website->save();

        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $analyticsId,
                'cdt' => now()->subDays($daysToSub)->timestamp,
                'token_auth' => config('analytics-service.admin_token'),
            ],
            'verify' => false,
        ]);

        $job = new MonitorWebsitesTracking();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id);

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getArchiving(), true)
                && empty($event->getArchived())
                && empty($event->getFailed());
        });
    }

    /**
     * Test job completed with failed website due to missing website into Analytics Service.
     */
    public function testMonitorCheckFailed(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
            'type' => WebsiteType::SECONDARY,
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subDays((int) config('wai.archive_expire') + 1),
        ]);

        $job = new MonitorWebsitesTracking();
        $job->handle();

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug, 'reason' => 'Invalid command for Analytics Service'], $event->getFailed(), true)
                && empty($event->getArchiving())
                && empty($event->getArchived());
        });
    }
}
