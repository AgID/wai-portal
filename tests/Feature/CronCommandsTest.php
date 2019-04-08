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
     * Test update IPA job route successful dispatching job.
     */
    public function testUpdateIPACron(): void
    {
        Queue::fake();
        $response = $this->get('/cron/updateipa?token=' . config('cron-auth.cron_token'));
        $response->assertStatus(202);

        Queue::assertPushed(ProcessIPAList::class);
    }

    /**
     * Test unauthorized access on update IPA job route blocked.
     */
    public function testUnauthorizedIPACron(): void
    {
        Queue::fake();
        $response = $this->get('/cron/updateipa');
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessIPAList::class);

        $response = $this->get('/cron/updateipa?token=' . md5('wrong_token'));
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessIPAList::class);
    }

    public function testCheckWebsitesCron()
    {
        Queue::fake();
        $response = $this->get('/cron/checkwebsites?token=' . config('cron-auth.cron_token'));
        $response->assertStatus(202);

        Queue::assertPushed(ProcessPendingWebsites::class);
    }

    public function testUnauthorizedCheckWebsitesCron(): void
    {
        Queue::fake();
        $response = $this->get('/cron/checkwebsites');
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessPendingWebsites::class);

        $response = $this->get('/cron/checkwebsites?token=' . md5('wrong_token'));
        $response->assertForbidden();
        Queue::assertNotPushed(ProcessPendingWebsites::class);
    }
}
