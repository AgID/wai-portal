<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Events\User\UserUpdated;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/**
 * User profile test.
 */
class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Pre-test setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    /**
     * Test profile update successful.
     */
    public function testUserProfileUpdateSuccessful(): void
    {
        $user = factory(User::class)->create();
        $this->actingAs($user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
            ])
            ->patch(
                route('user.profile.update'),
                [
                    'name' => $user->name,
                    'familyName' => $user->familyName,
                    'email' => 'new@email.local',
                ]
            )
            ->assertSessionDoesntHaveErrors([
                'email',
            ])
            ->assertRedirect(route('user.profile'));

        Event::assertDispatched(UserUpdated::class, function ($event) {
            return 'new@email.local' === $event->getUser()->email;
        });
    }

    /**
     * Test profile update fail due to field validation.
     */
    public function testUserProfileUpdateFailValidation(): void
    {
        $user = factory(User::class)->create();
        $this->actingAs($user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
            ])
            ->from(route('user.profile.edit'))
            ->patch(
                route('user.profile.update'),
                [
                    'email' => $user->email,
                ]
            )
            ->assertSessionHasErrors([
                'email',
            ])
            ->assertRedirect(route('user.profile.edit'));

        Event::assertNotDispatched(UserUpdated::class);
    }

    /**
     * Test super admin profile update successful.
     */
    public function testSuperAdminUserProfileUpdateSuccessful(): void
    {
        $user = factory(User::class)->create();
        Bouncer::scope()->to(0);
        $user->assign(UserRole::SUPER_ADMIN);
        $user->allow(UserPermission::ACCESS_ADMIN_AREA);

        $this->actingAs($user)
            ->patch(
                route('admin.user.profile.update'),
                [
                    'name' => 'Mario',
                    'familyName' => 'Rossi',
                    'email' => 'new@email.local',
                ]
            )
            ->assertSessionDoesntHaveErrors([
                'name',
                'familyName',
                'email',
            ])
            ->assertRedirect(route('admin.user.profile'));

        Event::assertDispatched(UserUpdated::class, function ($event) {
            $user = $event->getUser();

            return 'new@email.local' === $user->email
                && 'Mario' === $user->name
                && 'Rossi' === $user->familyName;
        });
    }

    /**
     * Test super admin profile fail due to field validation.
     */
    public function testSuperAdminUserProfileUpdateFailValidation(): void
    {
        $user = factory(User::class)->create();
        Bouncer::scope()->to(0);
        $user->assign(UserRole::SUPER_ADMIN);
        $user->allow(UserPermission::ACCESS_ADMIN_AREA);

        $this->actingAs($user)
            ->from(route('admin.user.profile.edit'))
            ->patch(route('admin.user.profile.update'))
            ->assertSessionHasErrors([
                'name',
                'familyName',
                'email',
            ])
            ->assertRedirect(route('admin.user.profile.edit'));

        Event::assertNotDispatched(UserUpdated::class);
    }
}
