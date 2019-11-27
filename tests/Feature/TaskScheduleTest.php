<?php

namespace Tests\Feature;

use App\Jobs\MonitorWebsitesTracking;
use App\Jobs\ProcessPendingWebsites;
use App\Jobs\ProcessPublicAdministrationsUpdateFromIpa;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class TaskScheduleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake();
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }
    public function testPublicAdministrationsUpdateFromIpa(): void
    {
        $scheduledStart = Carbon::now()->setHour(6)->setMinutes(30);
        Carbon::setTestNow($scheduledStart);
        $this->artisan('schedule:run');

        Bus::assertDispatched(ProcessPublicAdministrationsUpdateFromIpa::class);
    }

    public function testProcessPendingWebsites(): void
    {
        $scheduledStart = Carbon::now()->setMinutes(00);
        Carbon::setTestNow($scheduledStart);
        $this->artisan('schedule:run');

        Bus::assertDispatched(ProcessPendingWebsites::class, function ($job) {
            return !$job->executePurgeCheck;
        });
    }

    public function testProcessPendingWebsitesWithPurge(): void
    {
        $scheduledStart = Carbon::now()->setHour(4)->setMinutes(30);
        Carbon::setTestNow($scheduledStart);
        $this->artisan('schedule:run');

        Bus::assertDispatched(ProcessPendingWebsites::class, function ($job) {
            return $job->executePurgeCheck;
        });
    }

    public function testMonitorWebsitesTracking(): void
    {
        $scheduledStart = Carbon::now()->setHour(0)->setMinutes(00);
        Carbon::setTestNow($scheduledStart);
        $this->artisan('schedule:run');

        Bus::assertDispatched(MonitorWebsitesTracking::class);
    }
}
