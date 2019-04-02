<?php

namespace Tests\Feature;

use App\Models\PasswordResetToken;
use App\Models\PublicAdministration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * WAI models tests.
 */
class UserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test user creation routine.
     */
    public function testUserCreation(): void
    {
        $partialAnalyticsPassword = Str::random(rand(32, 48));

        $user = factory(User::class)->make([
            'partial_analytics_password' => $partialAnalyticsPassword,
        ]);

        $this->assertDatabaseMissing('users', ['email' => $user->email]);

        $user->save();

        $this->assertDatabaseHas('users', ['email' => $user->email]);

        $searchedUser = User::findByFiscalNumber($user->fiscalNumber);

        $this->assertNotNull($searchedUser);

        $this->assertEquals($user->name . ' ' . $user->familyName . ' [' . $user->email . ']', $searchedUser->getInfo());

        $analytics_password = md5($partialAnalyticsPassword . config('app.salt'));

        $this->assertEquals($analytics_password, $searchedUser->analytics_password);
    }

    /**
     * Test user soft delete and restore routine.
     *
     * @throws \Exception
     */
    public function testUserSoftDeleteAndRestore(): void
    {
        $user = factory(User::class)->create();

        $searchedUser = User::findByFiscalNumber($user->fiscalNumber);

        $searchedUser->delete();

        $this->assertSoftDeleted('users', ['id' => $searchedUser->id]);

        $searchedUser = User::findByFiscalNumber($user->fiscalNumber);

        $this->assertNull($searchedUser);

        $searchedUser = User::findTrashedByFiscalNumber($user->fiscalNumber);

        $this->assertNotNull($searchedUser);

        $searchedUser->restore();

        $searchedUser = User::findByFiscalNumber($user->fiscalNumber);

        $this->assertNotNull($searchedUser);
    }

    /**
     * Test users - public administrations relationship.
     */
    public function testPublicAdministrationRelation(): void
    {
        $firstUser = factory(User::class)->create();
        $secondUser = factory(User::class)->create();
        $thirdUser = factory(User::class)->create();

        $firstPA = factory(PublicAdministration::class)->create();
        $secondPA = factory(PublicAdministration::class)->create();
        $thirdPA = factory(PublicAdministration::class)->create();

        $firstUser->publicAdministrations()->sync([$firstPA->id, $secondPA->id]);
        $secondUser->publicAdministrations()->sync($thirdPA->id);
        $thirdUser->publicAdministrations()->sync($thirdPA->id);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $firstPA->id,
            'user_id' => $firstUser->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $secondPA->id,
            'user_id' => $firstUser->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $thirdPA->id,
            'user_id' => $secondUser->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $thirdUser->id,
            'user_id' => $thirdPA->id,
        ]);

        $searchedFirstUser = User::findByFiscalNumber($firstUser->fiscalNumber);
        $searchedSecondUser = User::findByFiscalNumber($secondUser->fiscalNumber);
        $searchedThirdUser = User::findByFiscalNumber($thirdUser->fiscalNumber);

        $this->assertCount(2, $searchedFirstUser->publicAdministrations()->get());
        $this->assertCount(1, $searchedSecondUser->publicAdministrations()->get());
        $this->assertCount(1, $searchedThirdUser->publicAdministrations()->get());

        $searchedFirstPA = PublicAdministration::findByIPACode($firstPA->ipa_code);
        $searchedSecondPA = PublicAdministration::findByIPACode($secondPA->ipa_code);
        $searchedThirdPA = PublicAdministration::findByIPACode($thirdPA->ipa_code);

        $this->assertCount(1, $searchedFirstPA->users()->get());
        $this->assertCount(1, $searchedSecondPA->users()->get());
        $this->assertCount(2, $searchedThirdPA->users()->get());
    }

    /**
     * Test user - password reset token relationship.
     */
    public function testPasswordResetTokenRelation(): void
    {
        $user = factory(User::class)->create();

        $this->assertFalse($user->passwordResetToken()->exists());

        $this->assertDatabaseMissing('password_reset_tokens', [
            'user_id' => $user->id,
        ]);

        $token = factory(PasswordResetToken::class)->create([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('password_reset_tokens', [
            'user_id' => $user->id,
            'token' => $token->token,
        ]);

        $searchedUser = User::findByFiscalNumber($user->fiscalNumber);

        $this->assertTrue($searchedUser->passwordResetToken()->exists());

        $this->assertEquals($token->token, $searchedUser->passwordResetToken->token);

        $this->assertFalse($user->isPasswordExpired());
    }

    /**
     * Test user password expiration.
     */
    public function testPasswordValidity(): void
    {
        $user = factory(User::class)->create([
            'password_changed_at' => null,
        ]);

        $this->assertFalse($user->isPasswordExpired());

        $user = factory(User::class)->create([
            'password_changed_at' => Carbon::now(),
        ]);

        $this->assertFalse($user->isPasswordExpired());

        $user = factory(User::class)->state('password_expired')->create();

        $this->assertTrue($user->isPasswordExpired());
    }
}
