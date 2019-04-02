<?php

namespace Tests\Unit;

use App\Models\Keyword;
use App\Models\PublicAdministration;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * WAI models tests.
 */
class KeywordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test keyword creation routine.
     */
    public function testWebsiteCreation(): void
    {
        $keyword = factory(Keyword::class)->make();

        $this->assertDatabaseMissing('keywords', ['name' => $keyword->name]);

        $keyword->save();

        $this->assertDatabaseHas('keywords', ['name' => $keyword->name]);

        $searched_keyword = Keyword::where('id', $keyword->id)->first();

        $this->assertNotNull($searched_keyword);
    }

    /**
     * Test keyword soft delete and restore routine.
     *
     * @throws \Exception
     */
    public function testKeywordSoftDeleteAndRestore(): void
    {
        $keyword = factory(Keyword::class)->create();

        $searchedKeyword = Keyword::where('id', $keyword->id)->first();

        $searchedKeyword->delete();

        $this->assertSoftDeleted('keywords', ['id' => $keyword->id]);

        $searchedKeyword = Keyword::where('id', $keyword->id)->first();

        $this->assertNull($searchedKeyword);

        $searchedKeyword = Keyword::onlyTrashed()->where('id', $keyword->id)->first();

        $this->assertNotNull($searchedKeyword);

        $searchedKeyword->restore();

        $searchedKeyword = Keyword::where('id', $keyword->id)->first();

        $this->assertNotNull($searchedKeyword);
    }

    /**
     * Test keyword - website relationship.
     */
    public function testWebsiteRelation(): void
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

        $firstKeyword->websites()->sync([$firstWebsite->id, $secondWebsite->id]);
        $secondKeyword->websites()->sync($secondWebsite->id);

        $this->assertDatabaseHas('keyword_website', [
            'keyword_id' => $firstKeyword->id,
            'website_id' => $firstWebsite->id,
        ]);

        $this->assertDatabaseHas('keyword_website', [
            'keyword_id' => $firstKeyword->id,
            'website_id' => $secondWebsite->id,
        ]);

        $this->assertDatabaseHas('keyword_website', [
            'keyword_id' => $secondKeyword->id,
            'website_id' => $secondWebsite->id,
        ]);

        $searchedFirstKeyword = Keyword::where('id', $firstKeyword->id)->first();
        $searchedSecondKeyword = Keyword::where('id', $secondKeyword->id)->first();

        $this->assertCount(2, $searchedFirstKeyword->websites()->get());
        $this->assertCount(1, $searchedSecondKeyword->websites()->get());
    }
}
