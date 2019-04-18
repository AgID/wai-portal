<?php

namespace Tests\Unit;

use App\Events\Jobs\WebsitesMonitoringCheckCompleted;
use App\Jobs\ProcessWebsitesMonitoring;
use App\Models\PublicAdministration;
use App\Models\Website;
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
        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subMonths((int) config('wai.archive_warning'))->subDay(),
        ]);

        $analyticsId = $this->app->make('analytics-service')->registerSite($website->name, $website->url, $publicAdministration->name);

        $website->analytics_id = $analyticsId;
        $website->save();

        $job = new ProcessWebsitesMonitoring();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id, config('analytics-service.admin_token'));

        Event::assertDispatched(WebsitesMonitoringCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getArchiving(), true)
                && empty($event->getArchived())
                && empty($event->getFailed());
        });
    }

    public function testMonitorCheckArchived(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->state('active')->create([
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subMonths((int) config('wai.archive_expiry'))->subDay(),
        ]);
        $analyticsId = $this->app->make('analytics-service')->registerSite($website->name, $website->url, $publicAdministration->name);

        $website->analytics_id = $analyticsId;
        $website->save();

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
            'created_at' => now()->subMonths(((int) config('wai.archive_expiry')) + 1),
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
