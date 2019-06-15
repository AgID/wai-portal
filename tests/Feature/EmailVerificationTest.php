<?php

namespace Tests\Feature;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Italia\SPIDAuth\SPIDUser;
use Tests\TestCase;

/**
 * Email verification related test.
 */
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * User to verify.
     *
     * @var User the user
     */
    private $user;

    /**
     * SPID user information.
     *
     * @var SPIDUser the fake SPID information
     */
    private $spidUser;

    /**
     * Pre-test setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->spidUser = new SPIDUser([
            'familyName' => 'Cognome',
            'name' => 'Nome',
        ]);
    }

    /**
     * Test email verification successful.
     */
    public function testEmailVerificationSuccessful(): void
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'uuid' => $this->user->uuid,
                'hash' => base64_encode(Hash::make($this->user->email)),
            ],
        );

        $response = $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->get($signedUrl);

        $response->assertRedirect(route('dashboard'));
    }

    /**
     * Test email verification fail due to wrong user email.
     */
    public function testEmailVerificationFailWrongEmail(): void
    {
        // Avoid mail sending on user update
        Mail::fake();
        $oldEmail = $this->user->email;
        $this->user->email = 'new@email.com';
        $this->user->save();

        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'uuid' => $this->user->uuid,
                'hash' => base64_encode(Hash::make($oldEmail)),
            ],
            );

        $response = $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->get($signedUrl);

        $response->assertForbidden();
    }
}
