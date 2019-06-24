<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserDeleted;
use App\Events\User\UserInvited;
use App\Events\User\UserUpdated;
use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/**
 * Super-admin user CRUD test.
 */
class CRUDAdminUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The super-admin user.
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
        Event::fake();
        $this->user = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => Date::now(),
        ]);

        Bouncer::dontCache();
        Bouncer::scope()->to(0);
        $this->user->assign(UserRole::SUPER_ADMIN);
        $this->user->allow(UserPermission::MANAGE_USERS);
        $this->user->allow(UserPermission::ACCESS_ADMIN_AREA);
    }

    /**
     * Test JSON data for datatable successful.
     */
    public function testDatatableData(): void
    {
        $this->actingAs($this->user)
            ->json(
                'GET',
                route('admin.users.data.json'),
                )
            ->assertOk()
            ->assertJsonFragment([
                'name' => implode(' ', [$this->user->familyName, $this->user->name]),
            ]);
    }

    /**
     * Test super-admin creation successful.
     */
    public function testCreateSuperAdminUserSuccessful(): void
    {
        $this->actingAs($this->user)
            ->post(
                route('admin.users.store'),
                [
                    '_token' => 'test',
                    'email' => 'new@email.local',
                    'name' => 'Mario',
                    'familyName' => 'Rossi',
                ],
                )
            ->assertSessionDoesntHaveErrors(
                [
                    'name',
                    'familyName',
                    'email',
                ]
            )
            ->assertRedirect(route('admin.users.index'));

        Event::assertDispatched(UserInvited::class, function ($event) {
            return
                'new@email.local' === $event->getUser()->email
                && null === $event->getPublicAdministration()
                && $event->getInvitedBy()->uuid === $this->user->uuid;
        });
    }

    /**
     * Test super-admin creation fails due to fields validation.
     */
    public function testCreateSuperAdminUserFailValidation(): void
    {
        $this->actingAs($this->user)
            ->from(route('admin.users.create'))
            ->post(
                route('admin.users.store'),
                [
                    '_token' => 'test',
                    'email' => $this->user->email,
                ]
            )
            ->assertRedirect(route('admin.users.create'))
            ->assertSessionHasErrors([
                'email',
                'name',
                'familyName',
            ]);

        Event::assertNotDispatched(UserInvited::class);
    }

    /**
     * Test super-admin update successful.
     */
    public function testUpdateSuperAdminUserSuccessful(): void
    {
        $user = factory(User::class)->state('invited')->create([
            'email_verified_at' => Date::now(),
        ]);

        Bouncer::scope()->to(0);
        $user->assign(UserRole::SUPER_ADMIN);
        $user->allow(UserPermission::ACCESS_ADMIN_AREA);
        $user->allow(UserPermission::MANAGE_USERS);

        $this->actingAs($this->user)
            ->patch(
                route('admin.users.update', ['user' => $user]),
                [
                    '_token' => 'test',
                    'email' => 'new@email.local',
                    'name' => 'Mario',
                    'familyName' => 'Rossi',
                ]
            )
            ->assertSessionDoesntHaveErrors([
                    'email',
                ]
            )
            ->assertRedirect(route('admin.users.index'));

        Event::assertDispatched(UserUpdated::class, function ($event) {
            $user = $event->getUser();

            return 'new@email.local' === $user->email
                && 'Mario' === $user->name
                && 'Rossi' === $user->familyName;
        });
    }

    /**
     * Test super-admin update fail due to field validation.
     */
    public function testUpdateSuperAdminUserFailValidation(): void
    {
        $this->actingAs($this->user)
            ->from(route('admin.users.edit', ['user' => $this->user]))
            ->patch(
                route('admin.users.update', ['user' => $this->user]),
                [
                    '_token' => 'test',
                ]
            )
            ->assertRedirect(route('admin.users.edit', ['user' => $this->user]))
            ->assertSessionHasErrors([
                'name',
                'familyName',
                'email',
            ]);

        Event::assertNotDispatched(UserUpdated::class);
    }

    /**
     * Test super-admin suspend successful.
     */
    public function testSuspendSuperAdminUserSuccessful(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
        ]);
        Bouncer::scope()->to(0);
        $user->assign(UserRole::SUPER_ADMIN);
        $user->allow(UserPermission::ACCESS_ADMIN_AREA);

        $this->actingAs($this->user)
            ->patch(
                route('admin.users.suspend', ['user' => $user]))
            ->assertJson([
                'result' => 'ok',
                'id' => $user->uuid,
                'status' => UserStatus::getDescription(UserStatus::SUSPENDED),
            ])
            ->assertOk();

        Event::assertDispatched(UserUpdated::class, function ($event) {
            return $event->getUser()->status->is(UserStatus::SUSPENDED);
        });
    }

    /**
     * Test super-admin suspend fail due to wrong status.
     */
    public function testSuspendSuperAdminUserFailAlreadySuspended(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::SUSPENDED,
        ]);
        Bouncer::scope()->to(0);
        $user->assign(UserRole::SUPER_ADMIN);
        $user->allow(UserPermission::ACCESS_ADMIN_AREA);

        $this->actingAs($this->user)
            ->patch(
                route('admin.users.suspend', ['user' => $user]))
            ->assertStatus(304);

        Event::assertNotDispatched(UserUpdated::class);
    }

    /**
     * Test super-admin suspend fail due to last super-admin.
     */
    public function testSuspendSuperAdminUserFailLastAdmin(): void
    {
        $this->actingAs($this->user)
            ->patch(
                route('admin.users.suspend', ['user' => $this->user]))
            ->assertStatus(400)
            ->assertJson([
                'result' => 'error',
                'message' => 'Invalid operation for current user',
                'code' => 0,
            ]);

        Event::assertNotDispatched(UserUpdated::class);
    }

    /**
     * Test super-admin reactivation successful.
     */
    public function testReactivateSuperAdminUserSuccessful(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::SUSPENDED,
        ]);

        $this->actingAs($this->user)
            ->patch(
                route('admin.users.reactivate', ['user' => $user]))
            ->assertJson([
                'result' => 'ok',
                'id' => $user->uuid,
                'status' => UserStatus::getDescription(UserStatus::INVITED),
            ])
            ->assertOk();

        Event::assertDispatched(UserUpdated::class, function ($event) {
            return $event->getUser()->status->is(UserStatus::INVITED);
        });
    }

    /**
     * Test super-admin reactivation fail due to wrong status.
     */
    public function testReactivateSuperAdminUserFailAlreadyActive(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
        ]);

        $this->actingAs($this->user)
            ->patch(
                route('admin.users.reactivate', ['user' => $user]))
            ->assertStatus(304);

        Event::assertNotDispatched(UserUpdated::class);
    }

    /**
     * Test JSON data for public administration users datatable successful.
     */
    public function testPublicAdministrationUserDatatableData(): void
    {
        $user = factory(User::class)->create();
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $publicAdministration->users()->sync([$user->id]);

        $this->actingAs($this->user)
            ->json(
                'GET',
                route('admin.publicAdministration.users.data.json', ['publicAdministration' => $publicAdministration]),
                )
            ->assertOk()
            ->assertJsonFragment([
                'name' => implode(' ', [$user->familyName, $user->name]),
            ]);
    }

    /**
     * Test normal user removal successful.
     */
    public function testDeleteUserSuccessful(): void
    {
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $user = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
        ]);
        $publicAdministration->users()->sync([$user->id]);
        Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
            $user->assign(UserRole::DELEGATED);
        });

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.users.delete', ['publicAdministration' => $publicAdministration, 'user' => $user]))
            ->assertOk();

        Event::assertDispatched(UserDeleted::class, function ($event) use ($user) {
            return $user->uuid === $event->getUser()->uuid;
        });
    }

    /**
     * Test normal user removal fail due to last public administration admin.
     */
    public function testDeleteUserFailLastAdmin(): void
    {
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $user = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
        ]);
        $publicAdministration->users()->sync([$user->id]);
        Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
            $user->assign(UserRole::ADMIN);
        });

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.users.delete', ['publicAdministration' => $publicAdministration, 'user' => $user]))
            ->assertStatus(400)
            ->assertJson([
                'result' => 'error',
                'message' => 'Impossibile rimuovere l\'utente ' . $user->getInfo() . ' in quanto ultimo amministratore attivo della P.A.',
            ]);

        Event::assertNotDispatched(UserDeleted::class);
    }

    /**
     * Test normal user removal fail due to user in 'pending' status.
     */
    public function testDeleteUserFailPending(): void
    {
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $user = factory(User::class)->create([
            'status' => UserStatus::PENDING,
        ]);
        $publicAdministration->users()->sync([$user->id]);
        Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
            $user->assign(UserRole::DELEGATED);
        });

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.users.delete', ['publicAdministration' => $publicAdministration, 'user' => $user]))
            ->assertStatus(400)
            ->assertJson([
                'result' => 'error',
                'message' => 'Impossibile rimuovere un utente in attesa di attivazione',
            ]);

        Event::assertNotDispatched(UserDeleted::class);
    }
}
