<?php

namespace Tests\Unit;

use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * WAI models tests.
 */
class PublicAdministrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test public administration creation routine.
     */
    public function testPublicAdministrationCreation(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->make();

        $this->assertDatabaseMissing('public_administrations', ['ipa_code' => $publicAdministration->ipa_code]);

        $publicAdministration->save();

        $this->assertDatabaseHas('public_administrations', ['ipa_code' => $publicAdministration->ipa_code]);

        $searched_pa = PublicAdministration::findByIPACode($publicAdministration->ipa_code);

        $this->assertNotNull($searched_pa);
    }

    /**
     * Test public administration soft delete and restore routine.
     *
     * @throws \Exception
     */
    public function testUserSoftDeleteAndRestore(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();

        $searchedPA = PublicAdministration::findByIPACode($publicAdministration->ipa_code);

        $searchedPA->delete();

        $this->assertSoftDeleted('public_administrations', ['id' => $searchedPA->id]);

        $searchedPA = PublicAdministration::findByIPACode($publicAdministration->ipa_code);

        $this->assertNull($searchedPA);

        $searchedPA = PublicAdministration::findTrashedByIPACode($publicAdministration->ipa_code);

        $this->assertNotNull($searchedPA);

        $searchedPA->restore();

        $searchedPA = PublicAdministration::findByIPACode($publicAdministration->ipa_code);

        $this->assertNotNull($searchedPA);
    }

    /**
     * Test users - public administrations relationship.
     */
    public function testUserRelation(): void
    {
        $firstUser = factory(User::class)->create();
        $secondUser = factory(User::class)->create();
        $thirdUser = factory(User::class)->create();

        $firstPA = factory(PublicAdministration::class)->create();
        $secondPA = factory(PublicAdministration::class)->create();
        $thirdPA = factory(PublicAdministration::class)->create();

        $firstPA->users()->sync([$firstUser->id, $secondUser->id]);
        $secondPA->users()->sync($secondUser->id);
        $thirdPA->users()->sync($thirdUser->id);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $firstPA->id,
            'user_id' => $firstUser->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $firstPA->id,
            'user_id' => $secondUser->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $secondPA->id,
            'user_id' => $secondUser->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $thirdPA->id,
            'user_id' => $thirdUser->id,
        ]);

        $searchedFirstPA = PublicAdministration::findByIPACode($firstPA->ipa_code);
        $searchedSecondPA = PublicAdministration::findByIPACode($secondPA->ipa_code);
        $searchedThirdPA = PublicAdministration::findByIPACode($thirdPA->ipa_code);

        $this->assertCount(2, $searchedFirstPA->users()->get());
        $this->assertCount(1, $searchedSecondPA->users()->get());
        $this->assertCount(1, $searchedThirdPA->users()->get());
    }

    /**
     * Test users - public administrations relationship.
     */
    public function testWebsiteRelation(): void
    {
        $firstPA = factory(PublicAdministration::class)->create();
        $secondPA = factory(PublicAdministration::class)->create();

        $firstWebsite = factory(Website::class)->create([
            'public_administration_id' => $firstPA->id,
        ]);

        do {
            $secondWebsite = factory(Website::class)->make([
                'public_administration_id' => $firstPA->id,
            ]);
        } while ($secondWebsite->slug === $firstWebsite->slug);

        $secondWebsite->save();

        do {
            $thirdWebsite = factory(Website::class)->make([
                'public_administration_id' => $secondPA->id,
            ]);
        } while ($thirdWebsite->slug === $firstWebsite->slug || $thirdWebsite->slug === $secondWebsite->slug);

        $thirdWebsite->save();

        $this->assertDatabaseHas('websites', [
            'id' => $firstWebsite->id,
            'public_administration_id' => $firstPA->id,
        ]);

        $this->assertDatabaseHas('websites', [
            'id' => $secondWebsite->id,
            'public_administration_id' => $firstPA->id,
        ]);

        $this->assertDatabaseHas('websites', [
            'id' => $thirdWebsite->id,
            'public_administration_id' => $secondPA->id,
        ]);

        $searchedFirstPA = PublicAdministration::findByIPACode($firstPA->ipa_code);
        $searchedSecondPA = PublicAdministration::findByIPACode($secondPA->ipa_code);

        $this->assertCount(2, $searchedFirstPA->websites()->get());
        $this->assertCount(1, $searchedSecondPA->websites()->get());
    }
}
