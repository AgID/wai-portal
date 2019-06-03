<?php

namespace Tests\Feature;

use App\Jobs\ProcessPendingWebsites;
use App\Jobs\ProcessPublicAdministrationsUpdateFromIpa;
use App\Jobs\ProcessWebsitesMonitoring;
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
     * Test update IPA job route successful dispatching job.
     */
    public function testUpdateIPACron(): void
    {
        $response = $this->get('/cron/updateipa?token=' . config('cron-auth.cron_token'));
        $response->assertStatus(202);

        Queue::assertPushed(ProcessPublicAdministrationsUpdateFromIpa::class);
    }

    /**
     * Test unauthorized access on update IPA job route blocked.
     */
    public function testUnauthorizedIPACron(): void
    {
        $response = $this->get('/cron/updateipa');
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessPublicAdministrationsUpdateFromIpa::class);

        $response = $this->get('/cron/updateipa?token=' . md5('wrong_token'));
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessPublicAdministrationsUpdateFromIpa::class);
    }

    /**
     * Test pending websites check job route successful dispatching job.
     */
    public function testCheckWebsitesCron(): void
    {
        $response = $this->get('/cron/checkpendingwebsites?token=' . config('cron-auth.cron_token'));
        $response->assertStatus(202);

        Queue::assertPushed(ProcessPendingWebsites::class);
    }

    /**
     * Test unauthorized access on pending websites check job route blocked.
     */
    public function testUnauthorizedCheckWebsitesCron(): void
    {
        $response = $this->get('/cron/checkpendingwebsites');
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessPendingWebsites::class);

        $response = $this->get('/cron/checkpendingwebsites?token=' . md5('wrong_token'));
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessPendingWebsites::class);
    }

    public function testMonitorWebsitesCron(): void
    {
        $response = $this->get('/cron/monitorwebsites?token=' . config('cron-auth.cron_token'));
        $response->assertStatus(202);

        Queue::assertPushed(ProcessWebsitesMonitoring::class);
    }

    public function testUnauthorizedMonitorWebsitesCron(): void
    {
        $response = $this->get('/cron/monitorwebsites');
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessWebsitesMonitoring::class);

        $response = $this->get('/cron/monitorwebsites?token=' . md5('wrong_token'));
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessWebsitesMonitoring::class);
    }
}
