<?php

namespace Tests\Unit;

use App\Events\Jobs\WebsiteIndexUpdateCompleted;
use App\Jobs\ProcessWebsitesIndex;
use App\Models\PublicAdministration;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Update websites index job test.
 */
class UpdateWebsiteIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pre-test setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    /**
     * Test job completed without index updates.
     */
    public function testWebsiteIndexUpdatedNoWebsites(): void
    {
        $job = new ProcessWebsitesIndex();
        $job->handle();

        Event::assertDispatched(WebsiteIndexUpdateCompleted::class, function ($event) {
            return empty($event->getInserted())
                && empty($event->getFailed());
        });
    }

    /**
     * Test index updated.
     */
    public function testWebsiteIndexUpdatedWebsiteAdded(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->create([
                'public_administration_id' => $publicAdministration->id,
            ]
        );

        $job = new ProcessWebsitesIndex();
        $job->handle();

        Event::assertDispatched(WebsiteIndexUpdateCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getInserted(), true)
                && empty($event->getFailed());
        });
    }

    /**
     * Test index updated even with soft-deleted users.
     */
    public function testWebsiteIndexUpdatedThrashedWebsiteAdded(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->create([
                'public_administration_id' => $publicAdministration->id,
            ]
        );
        $website->delete();

        $job = new ProcessWebsitesIndex();
        $job->handle();

        Event::assertDispatched(WebsiteIndexUpdateCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getInserted(), true)
                && empty($event->getFailed());
        });
    }
}
