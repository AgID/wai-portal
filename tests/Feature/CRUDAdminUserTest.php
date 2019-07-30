<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WebsiteAccessType;
use App\Events\User\UserDeleted;
use App\Events\User\UserInvited;
use App\Events\User\UserRestored;
use App\Events\User\UserUpdated;
use App\Events\User\UserWebsiteAccessChanged;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/**
 * Super admin user CRUD test.
 */
class CRUDAdminUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The super admin user.
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
     * Test super admin creation successful.
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
     * Test super admin creation fails due to fields validation.
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
     * Test super admin update successful.
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
     * Test super admin update fail due to field validation.
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
     * Test super admin suspend successful.
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
     * Test super admin suspend fail due to wrong status.
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
     * Test super admin suspend fail due to last super admin.
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
     * Test super admin reactivation successful.
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
     * Test super admin reactivation fail due to wrong status.
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
     * Test public administration admin creation successful.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     */
    public function testPublicAdministrationCreateAdminUserSuccessful(): void
    {
        $email = 'new@user.local';
        $fiscalNumber = 'ESXLKY44P09I168D';
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();

        $this->actingAs($this->user)
            ->post(
                route('admin.publicAdministration.users.store', ['publicAdministration' => $publicAdministration]),
                [
                    '_token' => 'test',
                    'email' => $email,
                    'fiscalNumber' => $fiscalNumber,
                    'isAdmin' => '1',
                ],
                )
            ->assertSessionDoesntHaveErrors(
                [
                    'email',
                    'fiscalNumber',
                    'isAdmin',
                ]
            )
            ->assertRedirect(route('admin.publicAdministration.users.index', ['publicAdministration' => $publicAdministration]));

        Event::assertDispatched(UserInvited::class, function ($event) use ($email, $publicAdministration) {
            return
                $email === $event->getUser()->email
                && $publicAdministration->ipa_code === $event->getPublicAdministration()->ipa_code
                && $event->getInvitedBy()->uuid === $this->user->uuid;
        });

        User::findByFiscalNumber($fiscalNumber)->deleteAnalyticsServiceAccount();
    }

    /**
     * Test public administration user creation successful.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     */
    public function testPublicAdministrationCreateUserSuccessful(): void
    {
        $email = 'new@user.local';
        $fiscalNumber = 'ESXLKY44P09I168D';
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $website = factory(Website::class)->create([
            'public_administration_id' => $publicAdministration->id,
        ]);

        $this->actingAs($this->user)
            ->post(
                route('admin.publicAdministration.users.store', ['publicAdministration' => $publicAdministration]),
                [
                    '_token' => 'test',
                    'email' => $email,
                    'fiscalNumber' => $fiscalNumber,
                    'websitesEnabled' => [
                        $website->id => 'enabled',
                    ],
                    'websitesPermissions' => [
                        $website->id => UserPermission::MANAGE_ANALYTICS,
                    ],
                ]
            )
            ->assertSessionDoesntHaveErrors(
                [
                    'email',
                    'fiscalNumber',
                    'isAdmin',
                    'websitesEnabled',
                    'websitesEnabled.*',
                    'websitesPermissions',
                    'websitesPermissions.*',
                ]
            )
            ->assertRedirect(route('admin.publicAdministration.users.index', ['publicAdministration' => $publicAdministration]));

        Event::assertDispatched(UserInvited::class, function ($event) use ($email, $publicAdministration) {
            return
                $email === $event->getUser()->email
                && $publicAdministration->ipa_code === $event->getPublicAdministration()->ipa_code
                && $event->getInvitedBy()->uuid === $this->user->uuid;
        });

        User::findByFiscalNumber($fiscalNumber)->deleteAnalyticsServiceAccount();
    }

    /**
     * Test user creation fails due to fields validation.
     */
    public function testPublicAdministrationCreateUserFailValidation(): void
    {
        $user = factory(User::class)->create();
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $publicAdministration->users()->sync([$user->id]);

        $this->actingAs($this->user)
            ->from(route('admin.publicAdministration.users.create', ['publicAdministration' => $publicAdministration]))
            ->post(
                route('admin.publicAdministration.users.store', ['publicAdministration' => $publicAdministration]),
                [
                    '_token' => 'test',
                    'email' => $user->email,
                    'fiscalNumber' => $user->fiscalNumber,
                ]
            )
            ->assertRedirect(route('admin.publicAdministration.users.create', ['publicAdministration' => $publicAdministration]))
            ->assertSessionHasErrors([
                'email',
                'fiscalNumber',
                'websitesEnabled',
            ]);

        Event::assertNotDispatched(UserInvited::class);
    }

    /**
     * Test user email update successful.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     */
    public function testPublicAdministrationUpdateUserEmailSuccessful(): void
    {
        $user = factory(User::class)->create();
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $publicAdministration->users()->sync([$user->id]);
        $website = factory(Website::class)->create([
            'public_administration_id' => $publicAdministration->id,
        ]);
        Bouncer::scope()->to($publicAdministration->id);
        $user->assign(UserRole::ADMIN);
        $user->registerAnalyticsServiceAccount();

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.users.update', ['publicAdministration' => $publicAdministration, 'user' => $this->user]),
                [
                    '_token' => 'test',
                    'email' => 'new@email.local',
                    'isAdmin' => '1',
                ]
            )
            ->assertSessionDoesntHaveErrors([
                    'email',
                    'isAdmin',
                    'websitesPermissions',
                ]
            );

        $this->user->refresh();
        Event::assertDispatched(UserUpdated::class, function ($event) {
            return 'new@email.local' === $event->getUser()->email;
        });

        $user->deleteAnalyticsServiceAccount();
    }

    /**
     * Test public administration user change role to admin from delegate successful.
     */
    public function testPublicAdministrationUpdateUserToAdminSuccessful(): void
    {
        $user = factory(User::class)->state('invited')->create([
            'email_verified_at' => Date::now(),
        ]);
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $publicAdministration->users()->sync([$user->id], false);
        $website = factory(Website::class)->create([
            'public_administration_id' => $publicAdministration->id,
        ]);
        $user->registerAnalyticsServiceAccount();

        Bouncer::scope()->to($publicAdministration->id);
        $user->assign(UserRole::DELEGATED);
        $user->allow(UserPermission::READ_ANALYTICS, $website);

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.users.update', ['publicAdministration' => $publicAdministration, 'user' => $user]),
                [
                    '_token' => 'test',
                    'email' => $user->email,
                    'isAdmin' => '1',
                ]
            )
            ->assertSessionDoesntHaveErrors([
                    'email',
                    'isAdmin',
                    'websitesPermissions',
                ]
            )
            ->assertRedirect(route('admin.publicAdministration.users.index', ['publicAdministration' => $publicAdministration]));

        $this->assertTrue($user->isA(UserRole::ADMIN));
        Event::assertDispatched(UserWebsiteAccessChanged::class, function ($event) use ($user, $website) {
            return $user->uuid === $event->getUser()->uuid
                && $website->slug === $event->getWebsite()->slug
                && $event->getAccessType()->is(WebsiteAccessType::WRITE);
        });

        $user->deleteAnalyticsServiceAccount();
    }

    /**
     * Test public administration user change role to delegate from admin successful.
     */
    public function testPublicAdministrationUpdateUserToDelegateSuccessful(): void
    {
        $user = factory(User::class)->create();
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $publicAdministration->users()->sync([$user->id], false);
        $website = factory(Website::class)->create([
            'public_administration_id' => $publicAdministration->id,
        ]);
        $user->registerAnalyticsServiceAccount();

        Bouncer::scope()->to($publicAdministration->id);
        $user->assign(UserRole::ADMIN);

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.users.update', ['publicAdministration' => $publicAdministration, 'user' => $user]),
                [
                    '_token' => 'test',
                    'email' => $user->email,
                    'websitesEnabled' => [
                        $website->id => 'enabled',
                    ],
                    'websitesPermissions' => [
                        $website->id => UserPermission::READ_ANALYTICS,
                    ],
                ]
            )
            ->assertSessionDoesntHaveErrors([
                'email',
                'isAdmin',
                'websitesEnabled',
                'websitesEnabled.*',
                'websitesPermissions',
                'websitesPermissions.*',
            ])
            ->assertRedirect(route('admin.publicAdministration.users.index', ['publicAdministration' => $publicAdministration]));

        Bouncer::refreshFor($user);
        $this->assertTrue($user->isA(UserRole::DELEGATED));
        Event::assertDispatched(UserWebsiteAccessChanged::class, function ($event) use ($user, $website) {
            return $user->uuid === $event->getUser()->uuid
                && $website->slug === $event->getWebsite()->slug
                && $event->getAccessType()->is(WebsiteAccessType::VIEW);
        });

        $user->deleteAnalyticsServiceAccount();
    }

    /**
     * Test user update fail due to field validation.
     */
    public function testPublicAdministrationUpdateUserFailValidation(): void
    {
        $user = factory(User::class)->state('active')->create();
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $publicAdministration->users()->sync([$user->id], false);
        $website = factory(Website::class)->create([
            'public_administration_id' => $publicAdministration->id,
        ]);
        Bouncer::scope()->to($publicAdministration->id);
        $user->assign(UserRole::ADMIN);
        $user->allow(UserPermission::MANAGE_USERS);

        $this->actingAs($this->user)
            ->from(route('admin.publicAdministration.users.edit', ['publicAdministration' => $publicAdministration, 'user' => $user]))
            ->patch(
                route('admin.publicAdministration.users.update', ['publicAdministration' => $publicAdministration, 'user' => $user]),
                [
                    '_token' => 'test',
                    'email' => $user->email,
                    'websiteEnabled' => [
                        $website->id => 'enabled',
                    ],
                    'websitesPermissions' => [
                        $website->id => UserPermission::READ_ANALYTICS,
                    ],
                ]
            )
            ->assertRedirect(route('admin.publicAdministration.users.edit', ['publicAdministration' => $publicAdministration, 'user' => $user]))
            ->assertSessionHasErrors([
                'isAdmin',
            ]);

        Event::assertNotDispatched(UserWebsiteAccessChanged::class);
    }

    /**
     * Test user suspend successful.
     */
    public function testSuspendUserSuccessful(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
        ]);
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $publicAdministration->users()->sync([$user->id], false);

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.users.suspend', ['publicAdministration' => $publicAdministration, 'user' => $user]))
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
     * Test user suspend fail due to wrong user status.
     */
    public function testSuspendUserFailAlreadySuspended(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::SUSPENDED,
        ]);
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $publicAdministration->users()->sync([$user->id], false);

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.users.suspend', ['publicAdministration' => $publicAdministration, 'user' => $user]))
            ->assertStatus(304);

        Event::assertNotDispatched(UserUpdated::class);
    }

    /**
     * Test user suspend fail due to last public administration admin.
     */
    public function testSuspendUserSuccessfulEvenLastAdmin(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
        ]);
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $publicAdministration->users()->sync([$user->id], false);
        Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
            $user->assign(UserRole::ADMIN);
            $user->allow(UserPermission::MANAGE_USERS);
        });

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.users.suspend', ['publicAdministration' => $publicAdministration, 'user' => $user]))
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
     * Test user suspend fail due to user in 'pending' status.
     */
    public function testSuspendUserFailPending(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::PENDING,
        ]);
        $publicAdministration = factory(PublicAdministration::class)->create();
        $publicAdministration->users()->sync([$user->id], false);

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.users.suspend', ['publicAdministration' => $publicAdministration, 'user' => $user]))
            ->assertStatus(400)
            ->assertJson([
                'result' => 'error',
                'message' => 'Invalid operation for current user status',
            ]);

        Event::assertNotDispatched(UserUpdated::class);
    }

    /**
     * Test user reactivation successful.
     */
    public function testReactivateUserSuccessful(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::SUSPENDED,
        ]);
        $publicAdministration = factory(PublicAdministration::class)->create();
        $publicAdministration->users()->sync([$user->id], false);

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.users.reactivate', ['publicAdministration' => $publicAdministration, 'user' => $user]))
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
     * Test user reactivation fail due to wrong user status.
     */
    public function testReactivateUserFailAlreadyActive(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
        ]);
        $publicAdministration = factory(PublicAdministration::class)->create();
        $publicAdministration->users()->sync([$user->id], false);

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.users.reactivate', ['publicAdministration' => $publicAdministration, 'user' => $user]))
            ->assertStatus(304);

        Event::assertNotDispatched(UserUpdated::class);
    }

    /**
     * Test normal user removal successful.
     */
    public function testPublicAdministrationDeleteUserSuccessful(): void
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

        Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
            $this->assertTrue($user->isA(UserRole::REMOVED));
        });

        Event::assertDispatched(UserDeleted::class, function ($event) use ($user) {
            return $user->uuid === $event->getUser()->uuid;
        });
    }

    /**
     * Test normal user removal fail due to last public administration admin.
     */
    public function testPublicAdministrationDeleteUserFailLastAdmin(): void
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
                'message' => "Impossibile rimuovere l'utente " . $user->getInfo() . ' in quanto ultimo amministratore attivo della P.A.',
            ]);

        Event::assertNotDispatched(UserDeleted::class);
    }

    /**
     * Test normal user removal fail due to user in 'pending' status.
     */
    public function testPublicAdministrationDeleteUserFailPending(): void
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

    /**
     * Test normal user restore successful.
     */
    public function testPublicAdministrationRestoreUserSuccessful(): void
    {
        $publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $user = factory(User::class)->create([
            'status' => UserStatus::PENDING,
            'deleted_at' => Date::now(),
        ]);
        $publicAdministration->users()->sync([$user->id]);
        Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
            $user->assign(UserRole::DELEGATED);
            $user->assign(UserRole::REMOVED);
        });

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.users.restore', ['publicAdministration' => $publicAdministration, 'trashed_user' => $user]))
            ->assertOk()
            ->assertJson([
                'result' => 'ok',
                'id' => $user->uuid,
                'status' => $user->status->description,
            ]);

        $user->refresh();

        $this->assertFalse($user->trashed());

        Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
            $this->assertFalse($user->isA(UserRole::REMOVED));
        });

        Event::assertDispatched(UserRestored::class, function ($event) use ($user) {
            return $user->uuid === $event->getUser()->uuid;
        });
    }
}
