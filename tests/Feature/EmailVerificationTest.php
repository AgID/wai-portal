<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserInvitationLinkExpired;
use App\Events\User\UserUpdated;
use App\Exceptions\ExpiredInvitationException;
use App\Exceptions\ExpiredVerificationException;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
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

        $this->withoutExceptionHandling();
    }

    /**
     * Test email verification successful.
     */
    public function testEmailVerificationSuccessful(): void
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addDays(Config::get('auth.verification.expire', 7)),
            [
                'uuid' => $this->user->uuid,
                'hash' => base64_encode(Hash::make($this->user->email)),
            ]
        );
        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->get($signedUrl)
            ->assertRedirect(route('dashboard'));
    }

    /**
     * Test email verification fail due to wrong user email.
     */
    public function testEmailVerificationFailWrongEmail(): void
    {
        Event::fake();
        $oldEmail = $this->user->email;
        $this->user->email = 'new@email.com';
        $this->user->save();

        Event::assertDispatched(UserUpdated::class);

        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addDays(Config::get('auth.verification.expire', 7)),
            [
                'uuid' => $this->user->uuid,
                'hash' => base64_encode(Hash::make($oldEmail)),
            ]
        );

        $this->expectException(AuthorizationException::class);
        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->get($signedUrl)
            ->assertForbidden();
    }

    /**
     * Test mail verification fail due to wrong URL signature.
     */
    public function testEmailVerificationFailWrongSignature(): void
    {
        Event::fake();

        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addDays(Config::get('auth.verification.expire', 7)), [
            'uuid' => $this->user->uuid,
            'hash' => base64_encode(Hash::make($this->user->email)),
        ]);
        $signedUrl = str_replace($this->user->uuid, Str::uuid()->toString(), $signedUrl);

        $this->expectException(InvalidSignatureException::class);
        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->get($signedUrl)
            ->assertForbidden()
            ->assertViewHas(
                'userMessage',
                __('Il link che hai usato non è valido oppure è scaduto.'),
                );

        Event::assertNotDispatched(UserInvitationLinkExpired::class);
    }

    /**
     * Test invitation verification fail due to expired URL.
     */
    public function testInvitationVerificationFailExpiredLink(): void
    {
        Event::fake();

        $this->user->status = UserStatus::INVITED;
        $this->user->save();
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->subDays(Config::get('auth.verification.expire', 7) + 1), [
            'uuid' => $this->user->uuid,
            'hash' => base64_encode(Hash::make($this->user->email)),
        ]);

        $this->expectException(ExpiredInvitationException::class);
        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->get($signedUrl)
            ->assertViewIs('auth.url_expired')
            ->assertViewHas(
                'invitation',
                true
            );

        Event::assertDispatched(UserInvitationLinkExpired::class, function ($event) {
            return $event->getUser()->uuid === $this->user->uuid;
        });
    }

    /**
     * Test email verification fail due to expired URL.
     */
    public function testEmailVerificationFailExpiredLink(): void
    {
        Event::fake();

        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->subDays(Config::get('auth.verification.expire', 7) + 1), [
            'uuid' => $this->user->uuid,
            'hash' => base64_encode(Hash::make($this->user->email)),
        ]);

        $this->expectException(ExpiredVerificationException::class);
        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->get($signedUrl)
            ->assertViewIs('auth.url_expired')
            ->assertViewHas(
                'invitation',
                false
            );

        Event::assertNotDispatched(UserInvitationLinkExpired::class);
    }

    /**
     * Test email verification fail due to expired URL for super-admin.
     */
    public function testEmailVerificationFailExpiredLinkSuperAdmin(): void
    {
        Event::fake();
        $this->user->assign(UserRole::SUPER_ADMIN);

        $signedUrl = URL::temporarySignedRoute(
            'admin.verification.verify',
            Carbon::now()->subDays(Config::get('auth.verification.expire', 7) + 1), [
            'uuid' => $this->user->uuid,
            'hash' => base64_encode(Hash::make($this->user->email)),
        ]);

        $this->expectException(InvalidSignatureException::class);
        $this->actingAs($this->user)
            ->get($signedUrl)
            ->assertViewIs('errors.403')
            ->assertViewHas(
                [
                    'userMessage' => __('Il link che hai usato non è valido oppure è scaduto.'),
                    'exception' => new InvalidSignatureException(),
                ]
            )
            ->assertForbidden();

        Event::assertNotDispatched(UserInvitationLinkExpired::class);
    }
}
