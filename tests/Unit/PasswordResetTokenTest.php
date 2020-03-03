<?php

namespace Tests\Unit;

use App\Models\PasswordResetToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * WAI models tests.
 */
class PasswordResetTokenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test password reset token creation routine.
     */
    public function testPasswordResetTokenCreation(): void
    {
        $user = factory(User::class)->create();

        $token = factory(PasswordResetToken::class)->make([
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseMissing('password_reset_tokens', ['user_id' => $user->id]);

        $token->save();

        $this->assertDatabaseHas('password_reset_tokens', ['user_id' => $user->id]);

        $searchedToken = PasswordResetToken::where('user_id', $user->id)->first();

        $this->assertNotNull($searchedToken);

        $this->assertEquals($token->token, $searchedToken->token);
    }

    public function testPasswordResetTokenDelete(): void
    {
        $user = factory(User::class)->create();

        factory(PasswordResetToken::class)->create([
            'user_id' => $user->id,
        ]);

        $searchedToken = PasswordResetToken::where('user_id', $user->id)->first();

        $searchedToken->delete();

        $searchedToken = PasswordResetToken::where('user_id', $user->id)->first();

        $this->assertNull($searchedToken);
    }
}
