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
        $partial_analytics_password = Str::random(rand(32, 48));

        $user = factory(User::class)->make([
            'partial_analytics_password' => $partial_analytics_password,
        ]);

        $this->assertDatabaseMissing('users', ['email' => $user->email]);

        $user->save();

        $this->assertDatabaseHas('users', ['email' => $user->email]);

        $searched_user = User::findByFiscalNumber($user->fiscalNumber);

        $this->assertNotNull($searched_user);

        $this->assertEquals($user->name . ' ' . $user->familyName . ' [' . $user->email . ']', $searched_user->getInfo());

        $analytics_password = md5($partial_analytics_password . config('app.salt'));

        $this->assertEquals($analytics_password, $searched_user->analytics_password);
    }

    /**
     * Test user soft delete and restore routine.
     *
     * @throws \Exception
     */
    public function testUserSoftDeleteAndRestore(): void
    {
        $user = factory(User::class)->create();

        $searched_user = User::findByFiscalNumber($user->fiscalNumber);

        $searched_user->delete();

        $this->assertSoftDeleted('users', ['id' => $searched_user->id]);

        $searched_user = User::findByFiscalNumber($user->fiscalNumber);

        $this->assertNull($searched_user);

        $searched_user = User::findTrashedByFiscalNumber($user->fiscalNumber);

        $this->assertNotNull($searched_user);

        $searched_user->restore();

        $searched_user = User::findByFiscalNumber($user->fiscalNumber);

        $this->assertNotNull($searched_user);
    }

    /**
     * Test users - public administrations relationship.
     */
    public function testPublicAdministrationRelation(): void
    {
        $first_user = factory(User::class)->create();
        $second_user = factory(User::class)->create();
        $third_user = factory(User::class)->create();

        $first_pa = factory(PublicAdministration::class)->create();
        $second_pa = factory(PublicAdministration::class)->create();
        $third_pa = factory(PublicAdministration::class)->create();

        $first_user->publicAdministrations()->sync([$first_pa->id, $second_pa->id]);
        $second_user->publicAdministrations()->sync($third_pa->id);
        $third_user->publicAdministrations()->sync($third_pa->id);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $first_pa->id,
            'user_id' => $first_user->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $second_pa->id,
            'user_id' => $first_user->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $third_pa->id,
            'user_id' => $second_user->id,
        ]);

        $this->assertDatabaseHas('public_administration_user', [
            'public_administration_id' => $third_user->id,
            'user_id' => $third_pa->id,
        ]);

        $searched_first_user = User::findByFiscalNumber($first_user->fiscalNumber);
        $searched_second_user = User::findByFiscalNumber($second_user->fiscalNumber);
        $searched_third_user = User::findByFiscalNumber($third_user->fiscalNumber);

        $this->assertCount(2, $searched_first_user->publicAdministrations()->get());
        $this->assertCount(1, $searched_second_user->publicAdministrations()->get());
        $this->assertCount(1, $searched_third_user->publicAdministrations()->get());

        $searched_first_pa = PublicAdministration::findByIPACode($first_pa->ipa_code);
        $searched_second_pa = PublicAdministration::findByIPACode($second_pa->ipa_code);
        $searched_third_pa = PublicAdministration::findByIPACode($third_pa->ipa_code);

        $this->assertCount(1, $searched_first_pa->users()->get());
        $this->assertCount(1, $searched_second_pa->users()->get());
        $this->assertCount(2, $searched_third_pa->users()->get());
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

        $searched_user = User::findByFiscalNumber($user->fiscalNumber);

        $this->assertTrue($searched_user->passwordResetToken()->exists());

        $this->assertEquals($token->token, $searched_user->passwordResetToken->token);

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
