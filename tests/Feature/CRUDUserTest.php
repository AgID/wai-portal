<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WebsiteAccessType;
use App\Events\User\UserDeleted;
use App\Events\User\UserInvited;
use App\Events\User\UserUpdated;
use App\Events\User\UserWebsiteAccessChanged;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Faker\Factory;
use Faker\Generator;
use Faker\Provider\it_IT\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Italia\SPIDAuth\SPIDUser;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/**
 * User CRUD test.
 */
class CRUDUserTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The user.
     *
     * @var User the user
     */
    private $user;

    /**
     * The SPID user.
     *
     * @var SPIDUser the SPID user
     */
    private $spidUser;

    /**
     * The public administration.
     *
     * @var PublicAdministration the public administration
     */
    private $publicAdministration;

    /**
     * The website.
     *
     * @var Website the website
     */
    private $website;

    /**
     * Fake data generator.
     *
     * @var Generator the generator
     */
    private $faker;

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
        $this->publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $this->publicAdministration->users()->sync([$this->user->id]);

        $this->website = factory(Website::class)->create([
            'public_administration_id' => $this->publicAdministration->id,
        ]);

        $this->spidUser = new SPIDUser([
            'fiscalNumber' => $this->user->fiscalNumber,
            'familyName' => $this->user->familyName,
            'name' => $this->user->name,
        ]);

        Bouncer::dontCache();
        Bouncer::scope()->to($this->publicAdministration->id);
        $this->user->assign(UserRole::ADMIN);
        $this->user->allow(UserPermission::MANAGE_USERS);

        $this->faker = Factory::create();
        $this->faker->addProvider(new Person($this->faker));
    }

    /**
     * Test JSON data for datatable successful.
     */
    public function testDatatableData(): void
    {
        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
            ])
            ->json(
                'GET',
                route('users.data.json'),
                )
            ->assertOk()
            ->assertJsonFragment([
                'name' => implode(' ', [$this->user->familyName, $this->user->name]),
            ]);
    }

    /**
     * Test public administration admin creation successful.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     */
    public function testCreateAdminUserSuccessful(): void
    {
        Event::fake();
        $email = $this->faker->unique()->safeEmail;
        $fiscalNumber = 'ESXLKY44P09I168D';

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
                '_token' => 'test',
            ])
            ->post(
                route('users.store'),
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
            ->assertRedirect(route('users.index'));

        Event::assertDispatched(UserInvited::class, function ($event) use ($email) {
            return
                $email === $event->getUser()->email
                && $this->publicAdministration->ipa_code === $event->getPublicAdministration()->ipa_code
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
    public function testCreateUserSuccessful(): void
    {
        $email = $this->faker->unique()->safeEmail;
        $fiscalNumber = 'ESXLKY44P09I168D';

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
                '_token' => 'test',
            ])
            ->post(
                route('users.store'),
                [
                    '_token' => 'test',
                    'email' => $email,
                    'fiscalNumber' => $fiscalNumber,
                    'websiteEnabled' => [
                        $this->website->id => 'enabled',
                    ],
                    'websitesPermissions' => [
                        $this->website->id => UserPermission::MANAGE_ANALYTICS,
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
            ->assertRedirect(route('users.index'));

        Event::assertDispatched(UserInvited::class, function ($event) use ($email) {
            return
                $email === $event->getUser()->email
                && $this->publicAdministration->ipa_code === $event->getPublicAdministration()->ipa_code
                && $event->getInvitedBy()->uuid === $this->user->uuid;
        });

        User::findByFiscalNumber($fiscalNumber)->deleteAnalyticsServiceAccount();
    }

    /**
     * Test user creation fails due to fields validation.
     */
    public function testCreateUserFailValidation(): void
    {
        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
                '_token' => 'test',
            ])
            ->from(route('users.create'))
            ->post(
                route('users.store'),
                [
                    '_token' => 'test',
                    'email' => $this->user->email,
                    'fiscalNumber' => $this->user->fiscalNumber,
                ]
            )
            ->assertRedirect(route('users.create'))
            ->assertSessionHasErrors([
                'email',
                'fiscalNumber',
                'websitesPermissions',
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
    public function testUpdateUserEmailSuccessful(): void
    {
        $this->user->registerAnalyticsServiceAccount();

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
                '_token' => 'test',
            ])
            ->patch(
                route('users.update', ['user' => $this->user]),
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

        $this->user->deleteAnalyticsServiceAccount();
    }

    /**
     * Test public administration user change role to admin from delegate successful.
     */
    public function testUpdateUserToAdminSuccessful(): void
    {
        $user = factory(User::class)->state('invited')->create([
            'email_verified_at' => Date::now(),
        ]);
        $this->publicAdministration->users()->sync([$user->id], false);
        $user->registerAnalyticsServiceAccount();

        Bouncer::scope()->to($this->publicAdministration->id);
        $user->assign(UserRole::DELEGATED);
        $user->allow(UserPermission::READ_ANALYTICS, $this->website);

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
                '_token' => 'test',
            ])
            ->patch(
                route('users.update', ['user' => $user]),
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
            ->assertRedirect(route('users.index'));

        $this->assertTrue($user->isA(UserRole::ADMIN));
        Event::assertDispatched(UserWebsiteAccessChanged::class, function ($event) use ($user) {
            return $user->uuid === $event->getUser()->uuid
                && $this->website->slug === $event->getWebsite()->slug
                && $event->getAccessType()->is(WebsiteAccessType::WRITE);
        });

        $user->deleteAnalyticsServiceAccount();
    }

    /**
     * Test public administration user change role to delegate from admin successful.
     */
    public function testUpdateUserToDelegateSuccessful(): void
    {
        $user = factory(User::class)->create();
        $this->publicAdministration->users()->sync([$user->id], false);
        $user->registerAnalyticsServiceAccount();

        Bouncer::scope()->to($this->publicAdministration->id);
        $user->assign(UserRole::ADMIN);

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
                '_token' => 'test',
            ])
            ->patch(
                route('users.update', ['user' => $user]),
                [
                    '_token' => 'test',
                    'email' => $user->email,
                    'websiteEnabled' => [
                        $this->website->id => 'enabled',
                    ],
                    'websitesPermissions' => [
                        $this->website->id => UserPermission::READ_ANALYTICS,
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
            ->assertRedirect(route('users.index'));

        Bouncer::refreshFor($user);
        $this->assertTrue($user->isA(UserRole::DELEGATED));
        Event::assertDispatched(UserWebsiteAccessChanged::class, function ($event) use ($user) {
            return $user->uuid === $event->getUser()->uuid
                && $this->website->slug === $event->getWebsite()->slug
                && $event->getAccessType()->is(WebsiteAccessType::VIEW);
        });

        $user->deleteAnalyticsServiceAccount();
    }

    /**
     * Test user update fail due to field validation.
     */
    public function testUpdateUserFailValidation(): void
    {
        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
                '_token' => 'test',
            ])
            ->from(route('users.edit', ['user' => $this->user]))
            ->patch(
                route('users.update', ['user' => $this->user]),
                [
                    '_token' => 'test',
                    'email' => $this->user->email,
                    'websiteEnabled' => [
                        $this->website->id => 'enabled',
                    ],
                    'websitesPermissions' => [
                        $this->website->id => UserPermission::READ_ANALYTICS,
                    ],
                ]
            )
            ->assertRedirect(route('users.edit', ['user' => $this->user]))
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
        $this->publicAdministration->users()->sync([$user->id], false);

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
            ])
            ->patch(
                route('users.suspend', ['user' => $user]))
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
        $this->publicAdministration->users()->sync([$user->id], false);

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
            ])
            ->patch(
                route('users.suspend', ['user' => $user]))
            ->assertStatus(304);

        Event::assertNotDispatched(UserUpdated::class);
    }

    /**
     * Test user suspend fail due to last public administration admin.
     */
    public function testSuspendUserFailLastAdmin(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::SUSPENDED,
        ]);
        $this->publicAdministration->users()->sync([$user->id], false);

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
            ])
            ->patch(
                route('users.suspend', ['user' => $this->user]))
            ->assertStatus(400)
            ->assertJson([
                'result' => 'error',
                'message' => 'Invalid operation for current user',
                'code' => 0,
            ]);

        Event::assertNotDispatched(UserUpdated::class);
    }

    /**
     * Test user suspend fail due to user in 'pending' status.
     */
    public function testSuspendUserFailPending(): void
    {
        Event::fakeFor(function () {
            $this->user->status = UserStatus::PENDING;
            $this->user->save();
        });
        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
            ])
            ->patch(
                route('users.suspend', ['user' => $this->user]))
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
        $this->publicAdministration->users()->sync([$user->id], false);

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
            ])
            ->patch(
                route('users.reactivate', ['user' => $user]))
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
        $this->publicAdministration->users()->sync([$user->id], false);

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
                '_token' => 'test',
            ])
            ->patch(
                route('users.reactivate', ['user' => $user]))
            ->assertStatus(304);

        Event::assertNotDispatched(UserUpdated::class);
    }
}
