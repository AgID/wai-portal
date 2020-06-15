<?php

namespace Tests\Unit;

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

        $searchedUser = User::findNotSuperAdminByFiscalNumber($user->fiscal_number);

        $this->assertNotNull($searchedUser);

        $this->assertEquals($user->full_name . ' [' . $user->email . ']', $searchedUser->info);

        $analytics_password = md5($partialAnalyticsPassword . config('app.salt'));

        $this->assertEquals($analytics_password, $searchedUser->analytics_password);
    }

    /**
     * Test user soft delete and restore routine.
     * Not used anymore.
     *
     * @throws \Exception
     */
    public function testUserSoftDeleteAndRestore(): void
    {
        $user = factory(User::class)->create();

        $searchedUser = User::findNotSuperAdminByFiscalNumber($user->fiscal_number);

        $searchedUser->delete();

        $this->assertSoftDeleted('users', ['id' => $searchedUser->id]);

        $searchedUser = User::findNotSuperAdminByFiscalNumber($user->fiscal_number);

        $this->assertNull($searchedUser);

        $searchedUser = User::findTrashedNotSuperAdminByFiscalNumber($user->fiscal_number);

        $this->assertNotNull($searchedUser);

        $searchedUser->restore();

        $searchedUser = User::findNotSuperAdminByFiscalNumber($user->fiscal_number);

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

        $firstPublicAdministration = factory(PublicAdministration::class)->create();
        $secondPublicAdministration = factory(PublicAdministration::class)->create();
        $thirdPublicAdministration = factory(PublicAdministration::class)->create();

        $firstUser->publicAdministrations()->sync([$firstPublicAdministration->id, $secondPublicAdministration->id]);
        $secondUser->publicAdministrations()->sync($thirdPublicAdministration->id);
        $thirdUser->publicAdministrations()->sync($thirdPublicAdministration->id);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $firstPublicAdministration->id,
            'user_id' => $firstUser->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $secondPublicAdministration->id,
            'user_id' => $firstUser->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $thirdPublicAdministration->id,
            'user_id' => $secondUser->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $thirdUser->id,
            'user_id' => $thirdPublicAdministration->id,
        ]);

        $searchedFirstUser = User::findNotSuperAdminByFiscalNumber($firstUser->fiscal_number);
        $searchedSecondUser = User::findNotSuperAdminByFiscalNumber($secondUser->fiscal_number);
        $searchedThirdUser = User::findNotSuperAdminByFiscalNumber($thirdUser->fiscal_number);

        $this->assertCount(2, $searchedFirstUser->publicAdministrations()->get());
        $this->assertCount(1, $searchedSecondUser->publicAdministrations()->get());
        $this->assertCount(1, $searchedThirdUser->publicAdministrations()->get());

        $searchedFirstPublicAdministration = PublicAdministration::findByIpaCode($firstPublicAdministration->ipa_code);
        $searchedSecondPublicAdministration = PublicAdministration::findByIpaCode($secondPublicAdministration->ipa_code);
        $searchedThirdPublicAdministration = PublicAdministration::findByIpaCode($thirdPublicAdministration->ipa_code);

        $this->assertCount(1, $searchedFirstPublicAdministration->users()->get());
        $this->assertCount(1, $searchedSecondPublicAdministration->users()->get());
        $this->assertCount(2, $searchedThirdPublicAdministration->users()->get());
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

        $searchedUser = User::findNotSuperAdminByFiscalNumber($user->fiscal_number);

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
