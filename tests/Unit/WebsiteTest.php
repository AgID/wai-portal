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
        $public_administration = factory(PublicAdministration::class)->create();

        $website = factory(Website::class)->make([
            'public_administration_id' => $public_administration->id,
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
        $public_administration = factory(PublicAdministration::class)->create();

        $website = factory(Website::class)->create([
            'public_administration_id' => $public_administration->id,
        ]);

        $searched_website = Website::where('id', $website->id)->first();

        $this->assertEquals(WebsiteStatus::PENDING, $searched_website->status);

        $searched_website->markActive();

        $searched_website = Website::where('id', $website->id)->first();

        $this->assertEquals(WebsiteStatus::ACTIVE, $searched_website->status);

        $searched_website->markArchived();

        $searched_website = Website::where('id', $website->id)->first();

        $this->assertEquals(WebsiteStatus::ARCHIVED, $searched_website->status);
    }

    /**
     * Test website soft delete and restore routine.
     *
     * @throws \Exception
     */
    public function testWebsiteSoftDeleteAndRestore(): void
    {
        $public_administration = factory(PublicAdministration::class)->create();

        $website = factory(Website::class)->create([
            'public_administration_id' => $public_administration->id,
        ]);

        $searched_website = Website::where('id', $website->id)->first();

        $searched_website->delete();

        $this->assertSoftDeleted('websites', ['id' => $website->id]);

        $searched_website = Website::where('id', $website->id)->first();

        $this->assertNull($searched_website);

        $searched_website = Website::onlyTrashed()->where('id', $website->id)->first();

        $this->assertNotNull($searched_website);

        $searched_website->restore();

        $searched_website = Website::where('id', $website->id)->first();

        $this->assertNotNull($searched_website);
    }

    /**
     * Test website - public administration relationship.
     */
    public function testPublicAdministrationRelation(): void
    {
        $first_pa = factory(PublicAdministration::class)->create();
        $second_pa = factory(PublicAdministration::class)->create();

        $first_website = factory(Website::class)->create([
            'public_administration_id' => $first_pa->id,
        ]);

        $second_website = factory(Website::class)->create([
            'public_administration_id' => $second_pa->id,
        ]);

        $searched_first_website = Website::where('id', $first_website->id)->first();
        $searched_second_website = Website::where('id', $second_website->id)->first();

        $this->assertEquals($first_pa->ipa_code, $searched_first_website->publicAdministration->ipa_code);
        $this->assertEquals($second_pa->ipa_code, $searched_second_website->publicAdministration->ipa_code);
    }

    /**
     * Test keyword - website relationship.
     */
    public function testKeywordRelation(): void
    {
        $first_keyword = factory(Keyword::class)->create();
        $second_keyword = factory(Keyword::class)->create();

        $public_administration = factory(PublicAdministration::class)->create();

        $first_website = factory(Website::class)->create([
            'public_administration_id' => $public_administration->id,
        ]);

        $second_website = factory(Website::class)->create([
            'public_administration_id' => $public_administration->id,
        ]);

        $first_website->keywords()->sync([$first_keyword->id, $second_keyword->id]);
        $second_website->keywords()->sync($second_keyword->id);

        $this->assertDatabaseHas('keyword_website', [
            'keyword_id' => $first_keyword->id,
            'website_id' => $first_website->id,
        ]);

        $this->assertDatabaseHas('keyword_website', [
            'keyword_id' => $second_keyword->id,
            'website_id' => $first_website->id,
        ]);

        $this->assertDatabaseHas('keyword_website', [
            'keyword_id' => $second_keyword->id,
            'website_id' => $second_website->id,
        ]);

        $searched_first_website = Website::where('id', $first_website->id)->first();
        $searched_second_website = Website::where('id', $second_website->id)->first();

        $this->assertCount(2, $searched_first_website->keywords()->get());
        $this->assertCount(1, $searched_second_website->keywords()->get());
    }
}
