<?php

namespace Tests\Feature;

use App\Jobs\MonitorWebsitesTracking;
use App\Jobs\ProcessPendingWebsites;
use App\Jobs\ProcessPublicAdministrationsUpdateFromIpa;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * CronJobs controller tests.
 */
class CronCommandsTest extends TestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        Queue::fake();
    }

    /**
     * Test update from IPA job route successful dispatching job.
     */
    public function testUpdateFromIpaCron(): void
    {
        $response = $this->get('/cron/update-from-ipa?token=' . config('cron-auth.cron_token'));
        $response->assertStatus(202);

        Queue::assertPushed(ProcessPublicAdministrationsUpdateFromIpa::class);
    }

    /**
     * Test unauthorized access on update from IPA job route blocked.
     */
    public function testUnauthorizedUpdateFromIpaCron(): void
    {
        $response = $this->get('/cron/update-from-ipa');
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessPublicAdministrationsUpdateFromIpa::class);

        $response = $this->get('/cron/update-from-ipa?token=' . md5('wrong_token'));
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessPublicAdministrationsUpdateFromIpa::class);
    }

    /**
     * Test pending websites check job route successful dispatching jobs.
     */
    public function testCheckWebsitesCron(): void
    {
        $response = $this->get('/cron/check-pending-websites?token=' . config('cron-auth.cron_token'));
        $response->assertStatus(202);

        Queue::assertPushed(ProcessPendingWebsites::class, function ($job) {
            return false === $job->executePurgeCheck;
        });

        $response = $this->get('/cron/check-pending-websites?token=' . config('cron-auth.cron_token') . '&purge=true');
        $response->assertStatus(202);

        Queue::assertPushed(ProcessPendingWebsites::class, function ($job) {
            return true === $job->executePurgeCheck;
        });
    }

    /**
     * Test unauthorized access on pending websites check job route blocked.
     */
    public function testUnauthorizedCheckWebsitesCron(): void
    {
        $response = $this->get('/cron/check-pending-websites');
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessPendingWebsites::class);

        $response = $this->get('/cron/check-pending-websites?token=' . md5('wrong_token'));
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessPendingWebsites::class);
    }

    public function testMonitorWebsitesCron(): void
    {
        $response = $this->get('/cron/monitor-websites?token=' . config('cron-auth.cron_token'));
        $response->assertStatus(202);

        Queue::assertPushed(MonitorWebsitesTracking::class);
    }

    public function testUnauthorizedMonitorWebsitesCron(): void
    {
        $response = $this->get('/cron/monitor-websites');
        $response->assertForbidden();
        Queue::assertNotPushed(MonitorWebsitesTracking::class);

        $response = $this->get('/cron/monitor-websites?token=' . md5('wrong_token'));
        $response->assertForbidden();
        Queue::assertNotPushed(MonitorWebsitesTracking::class);
    }
}
