<?php

namespace Tests\Unit;

use App\Enums\WebsiteStatus;
use App\Models\PublicAdministration;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * WAI models tests.
 */
class WebsiteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test registered and active websites counter.
     */
    public function testPublicAdministrationCounter(): void
    {
        $this->assertEquals(0, Website::getCount());

        Cache::forget(Website::WEBSITE_COUNT_KEY);
        $publicAdministration = factory(PublicAdministration::class)->create();
        $website = factory(Website::class)->make([
            'public_administration_id' => $publicAdministration->id,
        ]);
        $this->assertEquals(0, Website::getCount());

        Cache::forget(Website::WEBSITE_COUNT_KEY);
        $website->status = WebsiteStatus::ACTIVE;
        $website->save();
        $this->assertEquals(1, Website::getCount());
    }

    /**
     * Test website creation routine.
     */
    public function testWebsiteCreation(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();

        $website = factory(Website::class)->make([
            'public_administration_id' => $publicAdministration->id,
        ]);

        $this->assertDatabaseMissing('websites', ['slug' => $website->slug]);

        $website->save();

        $this->assertDatabaseHas('websites', ['slug' => $website->slug]);

        $searched_website = Website::where('id', $website->id)->first();

        $this->assertNotNull($searched_website);
    }

    /**
     * Testing website update routine.
     */
    public function testWebsiteUpdate(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();

        $website = factory(Website::class)->create([
            'public_administration_id' => $publicAdministration->id,
        ]);

        $searchedWebsite = Website::where('id', $website->id)->first();

        $this->assertEquals(WebsiteStatus::PENDING, $searchedWebsite->status->value);

        $searchedWebsite->markActive();

        $searchedWebsite = Website::where('id', $website->id)->first();

        $this->assertEquals(WebsiteStatus::ACTIVE, $searchedWebsite->status->value);

        $searchedWebsite->markArchived();

        $searchedWebsite = Website::where('id', $website->id)->first();

        $this->assertEquals(WebsiteStatus::ARCHIVED, $searchedWebsite->status->value);
    }

    /**
     * Test website soft delete and restore routine.
     *
     * @throws \Exception
     */
    public function testWebsiteSoftDeleteAndRestore(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();

        $website = factory(Website::class)->create([
            'public_administration_id' => $publicAdministration->id,
        ]);

        $searchedWebsite = Website::where('id', $website->id)->first();

        $searchedWebsite->delete();

        $this->assertSoftDeleted('websites', ['id' => $website->id]);

        $searchedWebsite = Website::where('id', $website->id)->first();

        $this->assertNull($searchedWebsite);

        $searchedWebsite = Website::onlyTrashed()->where('id', $website->id)->first();

        $this->assertNotNull($searchedWebsite);

        $searchedWebsite->restore();

        $searchedWebsite = Website::where('id', $website->id)->first();

        $this->assertNotNull($searchedWebsite);
    }

    /**
     * Test website - public administration relationship.
     */
    public function testPublicAdministrationRelation(): void
    {
        $firstPublicAdministration = factory(PublicAdministration::class)->create();
        $secondPublicAdministration = factory(PublicAdministration::class)->create();

        $firstWebsite = factory(Website::class)->create([
            'public_administration_id' => $firstPublicAdministration->id,
        ]);

        do {
            $secondWebsite = factory(Website::class)->make([
                'public_administration_id' => $secondPublicAdministration->id,
            ]);
        } while ($secondWebsite->slug === $firstWebsite->slug);
        $secondWebsite->save();

        $searchedFirstWebsite = Website::where('id', $firstWebsite->id)->first();
        $searchedSecondWebsite = Website::where('id', $secondWebsite->id)->first();

        $this->assertEquals($firstPublicAdministration->ipa_code, $searchedFirstWebsite->publicAdministration->ipa_code);
        $this->assertEquals($secondPublicAdministration->ipa_code, $searchedSecondWebsite->publicAdministration->ipa_code);
    }
}
