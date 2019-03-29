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
        $public_administration = factory(PublicAdministration::class)->make();

        $this->assertDatabaseMissing('public_administrations', ['ipa_code' => $public_administration->ipa_code]);

        $public_administration->save();

        $this->assertDatabaseHas('public_administrations', ['ipa_code' => $public_administration->ipa_code]);

        $searched_pa = PublicAdministration::findByIPACode($public_administration->ipa_code);

        $this->assertNotNull($searched_pa);
    }

    /**
     * Test public administration soft delete and restore routine.
     *
     * @throws \Exception
     */
    public function testUserSoftDeleteAndRestore(): void
    {
        $public_administration = factory(PublicAdministration::class)->create();

        $searched_pa = PublicAdministration::findByIPACode($public_administration->ipa_code);

        $searched_pa->delete();

        $this->assertSoftDeleted('public_administrations', ['id' => $searched_pa->id]);

        $searched_pa = PublicAdministration::findByIPACode($public_administration->ipa_code);

        $this->assertNull($searched_pa);

        $searched_pa = PublicAdministration::findTrashedByIPACode($public_administration->ipa_code);

        $this->assertNotNull($searched_pa);

        $searched_pa->restore();

        $searched_pa = PublicAdministration::findByIPACode($public_administration->ipa_code);

        $this->assertNotNull($searched_pa);
    }

    /**
     * Test users - public administrations relationship.
     */
    public function testUserRelation(): void
    {
        $first_user = factory(User::class)->create();
        $second_user = factory(User::class)->create();
        $third_user = factory(User::class)->create();

        $first_pa = factory(PublicAdministration::class)->create();
        $second_pa = factory(PublicAdministration::class)->create();
        $third_pa = factory(PublicAdministration::class)->create();

        $first_pa->users()->sync([$first_user->id, $second_user->id]);
        $second_pa->users()->sync($second_user->id);
        $third_pa->users()->sync($third_user->id);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $first_pa->id,
            'user_id' => $first_user->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $first_pa->id,
            'user_id' => $second_user->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $second_pa->id,
            'user_id' => $second_user->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $third_pa->id,
            'user_id' => $third_user->id,
        ]);

        $searched_first_pa = PublicAdministration::findByIPACode($first_pa->ipa_code);
        $searched_second_pa = PublicAdministration::findByIPACode($second_pa->ipa_code);
        $searched_third_pa = PublicAdministration::findByIPACode($third_pa->ipa_code);

        $this->assertCount(2, $searched_first_pa->users()->get());
        $this->assertCount(1, $searched_second_pa->users()->get());
        $this->assertCount(1, $searched_third_pa->users()->get());
    }

    /**
     * Test users - public administrations relationship.
     */
    public function testWebsiteRelation(): void
    {
        $first_pa = factory(PublicAdministration::class)->create();
        $second_pa = factory(PublicAdministration::class)->create();

        $first_website = factory(Website::class)->create([
            'public_administration_id' => $first_pa->id,
        ]);

        do {
            $second_website = factory(Website::class)->make([
                'public_administration_id' => $first_pa->id,
            ]);
        } while ($second_website->slug === $first_website->slug);

        $second_website->save();

        do {
            $third_website = factory(Website::class)->make([
                'public_administration_id' => $second_pa->id,
            ]);
        } while ($third_website->slug === $first_website->slug || $third_website->slug === $second_website->slug);

        $third_website->save();

        $this->assertDatabaseHas('websites', [
            'id' => $first_website->id,
            'public_administration_id' => $first_pa->id,
        ]);

        $this->assertDatabaseHas('websites', [
            'id' => $second_website->id,
            'public_administration_id' => $first_pa->id,
        ]);

        $this->assertDatabaseHas('websites', [
            'id' => $third_website->id,
            'public_administration_id' => $second_pa->id,
        ]);

        $searched_first_pa = PublicAdministration::findByIPACode($first_pa->ipa_code);
        $searched_second_pa = PublicAdministration::findByIPACode($second_pa->ipa_code);

        $this->assertCount(2, $searched_first_pa->websites()->get());
        $this->assertCount(1, $searched_second_pa->websites()->get());
    }
}
