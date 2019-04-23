<?php

namespace Tests\Unit;

use App\Events\Jobs\WebsitesMonitoringCheckCompleted;
use App\Jobs\ProcessWebsitesMonitoring;
use App\Models\PublicAdministration;
use App\Models\Website;
use Carbon\Carbon;
use GuzzleHttp\Client as TrackingClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class MonitorWebsitesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function testMonitorCheckCompleted(): void
    {
        $job = new ProcessWebsitesMonitoring();
        $job->handle();

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class);
    }

    public function testMonitorWebsiteArchiving(): void
    {
        $daysToSub = (int) config('wai.archive_expire') - (int) config('wai.archive_warning_daily_notification');

        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
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

        $job = new ProcessWebsitesMonitoring();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id, config('analytics-service.admin_token'));

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getArchiving(), true)
                && empty($event->getArchived())
                && empty($event->getFailed());
        });
    }

    public function testMonitorCheckWebsiteArchivingForWeeklyNotification(): void
    {
        $daysToSub = (int) config('wai.archive_warning') + 1;
        $notificationWeekDay = (int) config('wai.archive_warning_notification_day');

        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
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

        Carbon::now()->setWeekStartsAt(Carbon::SUNDAY);
        $date = Carbon::now()->startOfWeek()->addWeek(1)->addDays($notificationWeekDay);
        Carbon::setTestNow($date);

        $job = new ProcessWebsitesMonitoring();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id, config('analytics-service.admin_token'));

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getArchiving(), true)
                && empty($event->getArchived())
                && empty($event->getFailed());
        });
    }

    public function testMonitorCheckWebsiteNotArchiving(): void
    {
        $daysToSub = (int) config('wai.archive_warning') + 1;
        $notificationWeekDay = (int) config('wai.archive_warning_notification_day');

        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
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
                'cdt' => now()->subDays($daysToSub + 1)->timestamp,
                'token_auth' => config('analytics-service.admin_token'),
            ],
            'verify' => false,
        ]);

        Carbon::now()->setWeekStartsAt(Carbon::SUNDAY);
        $date = Carbon::now()->startOfWeek()->addWeek(1)->addDays($notificationWeekDay + 1);
        Carbon::setTestNow($date);

        $job = new ProcessWebsitesMonitoring();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id, config('analytics-service.admin_token'));

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) {
            return empty($event->getArchiving())
                && empty($event->getArchived())
                && empty($event->getFailed());
        });
    }

    public function testMonitorCheckArchived(): void
    {
        $daysToSub = (int) config('wai.archive_expire') + 1;

        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
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

        $job = new ProcessWebsitesMonitoring();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id, config('analytics-service.admin_token'));

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getArchived(), true)
                && empty($event->getArchiving())
                && empty($event->getFailed());
        });
    }

    public function testMonitorCheckFailed(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subDays((int) config('wai.archive_expire') + 1),
        ]);

        $job = new ProcessWebsitesMonitoring();
        $job->handle();

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug, 'reason' => 'Invalid command for Analytics Service'], $event->getFailed(), true)
                && empty($event->getArchiving())
                && empty($event->getArchived());
        });
    }
}
