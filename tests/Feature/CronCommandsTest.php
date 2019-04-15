<?php

namespace Tests\Feature;

use App\Jobs\ProcessIPAList;
use App\Jobs\ProcessPendingWebsites;
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

        Queue::assertPushed(ProcessIPAList::class);
    }

    /**
     * Test unauthorized access on update IPA job route blocked.
     */
    public function testUnauthorizedIPACron(): void
    {
        $response = $this->get('/cron/updateipa');
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessIPAList::class);

        $response = $this->get('/cron/updateipa?token=' . md5('wrong_token'));
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessIPAList::class);
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
}
