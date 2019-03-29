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

        $searched_keyword = Keyword::where('id', $keyword->id)->first();

        $searched_keyword->delete();

        $this->assertSoftDeleted('keywords', ['id' => $keyword->id]);

        $searched_keyword = Keyword::where('id', $keyword->id)->first();

        $this->assertNull($searched_keyword);

        $searched_keyword = Keyword::onlyTrashed()->where('id', $keyword->id)->first();

        $this->assertNotNull($searched_keyword);

        $searched_keyword->restore();

        $searched_keyword = Keyword::where('id', $keyword->id)->first();

        $this->assertNotNull($searched_keyword);
    }

    /**
     * Test keyword - website relationship.
     */
    public function testWebsiteRelation(): void
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

        $first_keyword->websites()->sync([$first_website->id, $second_website->id]);
        $second_keyword->websites()->sync($second_website->id);

        $this->assertDatabaseHas('keyword_website', [
            'keyword_id' => $first_keyword->id,
            'website_id' => $first_website->id,
        ]);

        $this->assertDatabaseHas('keyword_website', [
            'keyword_id' => $first_keyword->id,
            'website_id' => $second_website->id,
        ]);

        $this->assertDatabaseHas('keyword_website', [
            'keyword_id' => $second_keyword->id,
            'website_id' => $second_website->id,
        ]);

        $searched_first_keyword = Keyword::where('id', $first_keyword->id)->first();
        $searched_second_keyword = Keyword::where('id', $second_keyword->id)->first();

        $this->assertCount(2, $searched_first_keyword->websites()->get());
        $this->assertCount(1, $searched_second_keyword->websites()->get());
    }
}
