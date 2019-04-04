<?php

namespace Tests\Unit;

use App\Enums\WebsiteStatus;
use App\Models\Keyword;
use App\Models\PublicAdministration;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * WAI models tests.
 */
class WebsiteTest extends TestCase
{
    use RefreshDatabase;

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

        $this->assertEquals(WebsiteStatus::PENDING, $searchedWebsite->status);

        $searchedWebsite->markActive();

        $searchedWebsite = Website::where('id', $website->id)->first();

        $this->assertEquals(WebsiteStatus::ACTIVE, $searchedWebsite->status);

        $searchedWebsite->markArchived();

        $searchedWebsite = Website::where('id', $website->id)->first();

        $this->assertEquals(WebsiteStatus::ARCHIVED, $searchedWebsite->status);
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
        $firstPA = factory(PublicAdministration::class)->create();
        $secondPA = factory(PublicAdministration::class)->create();

        $firstWebsite = factory(Website::class)->create([
            'public_administration_id' => $firstPA->id,
        ]);

        do {
            $secondWebsite = factory(Website::class)->make([
                'public_administration_id' => $secondPA->id,
            ]);
        } while ($secondWebsite->slug === $firstWebsite->slug);
        $secondWebsite->save();

        $searchedFirstWebsite = Website::where('id', $firstWebsite->id)->first();
        $searchedSecondWebsite = Website::where('id', $secondWebsite->id)->first();

        $this->assertEquals($firstPA->ipa_code, $searchedFirstWebsite->publicAdministration->ipa_code);
        $this->assertEquals($secondPA->ipa_code, $searchedSecondWebsite->publicAdministration->ipa_code);
    }

    /**
     * Test keyword - website relationship.
     */
    public function testKeywordRelation(): void
    {
        $firstKeyword = factory(Keyword::class)->create();
        do {
            $secondKeyword = factory(Keyword::class)->make();
        } while ($firstKeyword->vocabulary === $secondKeyword->vocabulary && $firstKeyword->id_vocabulary === $secondKeyword->id_vocabulary);
        $secondKeyword->save();

        $publicAdministration = factory(PublicAdministration::class)->create();

        $firstWebsite = factory(Website::class)->create([
            'public_administration_id' => $publicAdministration->id,
        ]);

        do {
            $secondWebsite = factory(Website::class)->make([
                'public_administration_id' => $publicAdministration->id,
            ]);
        } while ($secondWebsite->slug === $firstWebsite->slug);
        $secondWebsite->save();

        $firstWebsite->keywords()->sync([$firstKeyword->id, $secondKeyword->id]);
        $secondWebsite->keywords()->sync($secondKeyword->id);

        $this->assertDatabaseHas('keyword_website', [
            'keyword_id' => $firstKeyword->id,
            'website_id' => $firstWebsite->id,
        ]);

        $this->assertDatabaseHas('keyword_website', [
            'keyword_id' => $secondKeyword->id,
            'website_id' => $firstWebsite->id,
        ]);

        $this->assertDatabaseHas('keyword_website', [
            'keyword_id' => $secondKeyword->id,
            'website_id' => $secondWebsite->id,
        ]);

        $searchedFirstWebsite = Website::where('id', $firstWebsite->id)->first();
        $searchedSecondWebsite = Website::where('id', $secondWebsite->id)->first();

        $this->assertCount(2, $searchedFirstWebsite->keywords()->get());
        $this->assertCount(1, $searchedSecondWebsite->keywords()->get());
    }
}
