<?php

namespace Tests\Unit;

use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * WAI models tests.
 */
class PublicAdministrationTest extends TestCase
{
    use RefreshDatabase;

    public function testPublicAdministrationCounter(): void
    {
        $this->assertEquals(0, PublicAdministration::getCount());

        Cache::forget(PublicAdministration::PUBLIC_ADMINISTRATION_COUNT_KEY);
        factory(PublicAdministration::class)->create();

        $this->assertEquals(1, PublicAdministration::getCount());
    }

    /**
     * Test public administration creation routine.
     */
    public function testPublicAdministrationCreation(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->make();

        $this->assertDatabaseMissing('public_administrations', ['ipa_code' => $publicAdministration->ipa_code]);

        $publicAdministration->save();

        $this->assertDatabaseHas('public_administrations', ['ipa_code' => $publicAdministration->ipa_code]);

        $searched_pa = PublicAdministration::findByIpaCode($publicAdministration->ipa_code);

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

        $searchedPublicAdministration = PublicAdministration::findByIpaCode($publicAdministration->ipa_code);

        $searchedPublicAdministration->delete();

        $this->assertSoftDeleted('public_administrations', ['id' => $searchedPublicAdministration->id]);

        $searchedPublicAdministration = PublicAdministration::findByIpaCode($publicAdministration->ipa_code);

        $this->assertNull($searchedPublicAdministration);

        $searchedPublicAdministration = PublicAdministration::findTrashedByIpaCode($publicAdministration->ipa_code);

        $this->assertNotNull($searchedPublicAdministration);

        $searchedPublicAdministration->restore();

        $searchedPublicAdministration = PublicAdministration::findByIpaCode($publicAdministration->ipa_code);

        $this->assertNotNull($searchedPublicAdministration);
    }

    /**
     * Test users - public administrations relationship.
     */
    public function testUserRelation(): void
    {
        $firstUser = factory(User::class)->create();
        $secondUser = factory(User::class)->create();
        $thirdUser = factory(User::class)->create();

        $firstPublicAdministration = factory(PublicAdministration::class)->create();
        $secondPublicAdministration = factory(PublicAdministration::class)->create();
        $thirdPublicAdministration = factory(PublicAdministration::class)->create();

        $firstPublicAdministration->users()->sync([$firstUser->id, $secondUser->id]);
        $secondPublicAdministration->users()->sync($secondUser->id);
        $thirdPublicAdministration->users()->sync($thirdUser->id);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $firstPublicAdministration->id,
            'user_id' => $firstUser->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $firstPublicAdministration->id,
            'user_id' => $secondUser->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $secondPublicAdministration->id,
            'user_id' => $secondUser->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $thirdPublicAdministration->id,
            'user_id' => $thirdUser->id,
        ]);

        $searchedFirstPublicAdministration = PublicAdministration::findByIpaCode($firstPublicAdministration->ipa_code);
        $searchedSecondPublicAdministration = PublicAdministration::findByIpaCode($secondPublicAdministration->ipa_code);
        $searchedThirdPublicAdministration = PublicAdministration::findByIpaCode($thirdPublicAdministration->ipa_code);

        $this->assertCount(2, $searchedFirstPublicAdministration->users()->get());
        $this->assertCount(1, $searchedSecondPublicAdministration->users()->get());
        $this->assertCount(1, $searchedThirdPublicAdministration->users()->get());
    }

    /**
     * Test users - public administrations relationship.
     */
    public function testWebsiteRelation(): void
    {
        $firstPublicAdministration = factory(PublicAdministration::class)->create();
        $secondPublicAdministration = factory(PublicAdministration::class)->create();

        $firstWebsite = factory(Website::class)->create([
            'public_administration_id' => $firstPublicAdministration->id,
        ]);

        do {
            $secondWebsite = factory(Website::class)->make([
                'public_administration_id' => $firstPublicAdministration->id,
            ]);
        } while ($secondWebsite->slug === $firstWebsite->slug);

        $secondWebsite->save();

        do {
            $thirdWebsite = factory(Website::class)->make([
                'public_administration_id' => $secondPublicAdministration->id,
            ]);
        } while ($thirdWebsite->slug === $firstWebsite->slug || $thirdWebsite->slug === $secondWebsite->slug);

        $thirdWebsite->save();

        $this->assertDatabaseHas('websites', [
            'id' => $firstWebsite->id,
            'public_administration_id' => $firstPublicAdministration->id,
        ]);

        $this->assertDatabaseHas('websites', [
            'id' => $secondWebsite->id,
            'public_administration_id' => $firstPublicAdministration->id,
        ]);

        $this->assertDatabaseHas('websites', [
            'id' => $thirdWebsite->id,
            'public_administration_id' => $secondPublicAdministration->id,
        ]);

        $searchedFirstPublicAdministration = PublicAdministration::findByIpaCode($firstPublicAdministration->ipa_code);
        $searchedSecondPublicAdministration = PublicAdministration::findByIpaCode($secondPublicAdministration->ipa_code);

        $this->assertCount(2, $searchedFirstPublicAdministration->websites()->get());
        $this->assertCount(1, $searchedSecondPublicAdministration->websites()->get());
    }
}
