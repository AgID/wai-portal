<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Events\User\UserUpdated;
use App\Jobs\ClearPasswordResetToken;
use App\Models\User;
use App\Notifications\PasswordResetRequestEmail;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/**
 * Super admin password management test.
 */
class SuperAdminUserPasswordTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Super admin user.
     *
     * @var User the user
     */
    private $user;

    /**
     * Pre-test setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Simulate another non super-admin user with same email address
        $nonSuperAdminUser = factory(User::class)->state('active')->create();

        $this->user = factory(User::class)->state('active')->create([
            'email' => $nonSuperAdminUser->email,
        ]);

        Bouncer::dontCache();
        Bouncer::scope()->to(0);
        $this->user->assign(UserRole::SUPER_ADMIN);
        $this->user->allow(UserPermission::ACCESS_ADMIN_AREA);
    }

    /**
     * Test password reset email send successful.
     */
    public function testSendResetPasswordSuccessful(): void
    {
        Queue::fake();
        Notification::fake();
        $this->post(route('admin.password.reset.send'), [
            'email' => $this->user->email,
        ])
        ->assertRedirect(route('home'));

        Notification::assertSentTo([$this->user], PasswordResetRequestEmail::class);
        Queue::assertPushed(ClearPasswordResetToken::class);
    }

    /**
     * Test password reset email send fail due to not existing email.
     */
    public function testSendResetPasswordWrongEmail(): void
    {
        Queue::fake();
        Notification::fake();
        $this->post(route('admin.password.reset.send'), [
            'email' => 'nonexisting@webanalytics.italia.it',
        ])
        ->assertRedirect(route('home'));

        Notification::assertNothingSent();
        Queue::assertNotPushed(ClearPasswordResetToken::class);
    }

    /**
     * Test password reset successful.
     */
    public function testPasswordResetSuccessful(): void
    {
        $token = hash_hmac('sha256', Str::random(40), config('app.key'));
        $this->user->passwordResetToken()->create([
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        Event::fake();

        $this->post(route('admin.password.reset'), [
            'email' => $this->user->email,
            'token' => $token,
            'password' => 'Nu0vaP4ssword!',
            'password_confirmation' => 'Nu0vaP4ssword!',
        ])
        ->assertRedirect(route('admin.dashboard'));

        Event::assertDispatched(UserUpdated::class);
        Event::assertDispatched(PasswordReset::class, function ($event) {
            return $this->user->is($event->user);
        });
    }

    /**
     * Test password reset fail due to invalid token.
     */
    public function testPasswordResetFailToken(): void
    {
        Event::fake();
        $token = hash_hmac('sha256', Str::random(40), config('app.key'));

        $this->post(route('admin.password.reset'), [
            'email' => $this->user->email,
            'token' => $token,
            'password' => 'Nu0vaP4ssword!',
            'password_confirmation' => 'Nu0vaP4ssword!',
        ])
        ->assertRedirect(route('admin.password.reset.show'));

        Event::assertNotDispatched(UserUpdated::class);
        Event::assertNotDispatched(PasswordReset::class);
    }

    /**
     * Test password change successful.
     */
    public function testPasswordChangeSuccessful(): void
    {
        $this->user->email_verified_at = Date::now();

        Event::fake();

        $this->actingAs($this->user)
            ->post(route('admin.password.change'), [
                'password' => 'Nu0vaP4ssword!',
                'password_confirmation' => 'Nu0vaP4ssword!',
            ])
            ->assertRedirect(route('admin.dashboard'));

        Event::assertDispatched(UserUpdated::class, function ($event) {
            return Hash::check('Nu0vaP4ssword!', $event->getUser()->password);
        });
    }

    /**
     * Test password change fail due to field validation.
     */
    public function testPasswordChangeFail(): void
    {
        $this->user->email_verified_at = Date::now();
        Event::fake();

        $this->actingAs($this->user)
            ->from(route('admin.password.change.show'))
            ->post(route('admin.password.change'), [
                'password' => 'passwordsemplice',
                'password_confirmation' => 'passwordsemplice',
            ])
            ->assertRedirect(route('admin.password.change.show'));

        Event::assertNotDispatched(UserUpdated::class);
    }
}
