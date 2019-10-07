<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Events\User\UserInvitationLinkExpired;
use App\Events\User\UserUpdated;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'uuid' => $this->user->uuid,
                'hash' => base64_encode(Hash::make($oldEmail)),
            ]
        );

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
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)), [
            'uuid' => $this->user->uuid,
            'hash' => base64_encode(Hash::make($this->user->email)),
        ]);
        $signedUrl = str_replace($this->user->uuid, Str::uuid()->toString(), $signedUrl);

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
     * Test mail verification fail due to expired URL validity.
     */
    public function testEmailVerificationFailExpiredLink(): void
    {
        Event::fake();

        $this->user->status = UserStatus::INVITED;
        $this->user->save();
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->subMinutes(Config::get('auth.verification.expire', 60) + 1), [
            'uuid' => $this->user->uuid,
            'hash' => base64_encode(Hash::make($this->user->email)),
        ]);

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->get($signedUrl)
            ->assertRedirect(route('home'))
            ->assertSessionHas(
                'notification',
                [
                    'title' => __('errore nella richiesta'),
                    'message' => __('L\'invito che hai usato non è più valido. Contatta un amministratore per ricevere uno nuovo link.'),
                    'status' => 'error',
                    'icon' => 'it-close-circle',
                ]
            );

        Event::assertDispatched(UserInvitationLinkExpired::class, function ($event) {
            return $event->getUser()->uuid === $this->user->uuid;
        });
    }
}
