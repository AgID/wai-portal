<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\PublicAdministration\PublicAdministrationRegistered;
use App\Events\User\UserInvited;
use App\Events\Website\WebsiteAdded;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Faker\Factory;
use Faker\Generator;
use Faker\Provider\it_IT\Person;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Italia\SPIDAuth\SPIDUser;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/**
 * User profile test.
 */
class InviteUserTest extends TestCase
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

        $this->publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();

        $this->user = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => Date::now(),
        ]);
        $this->publicAdministration->users()->sync([$this->user->id => ['user_email' => $this->user->email, 'user_status' => UserStatus::ACTIVE]]);

        $this->secondUser = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => Date::now(),
        ]);

        $this->website = factory(Website::class)->create([
            'public_administration_id' => $this->publicAdministration->id,
        ]);

        $this->spidUser = new SPIDUser([
            'fiscalNumber' => $this->user->fiscal_number,
            'familyName' => $this->user->family_name,
            'name' => $this->user->name,
        ]);
        $this->secondSpidUser = new SPIDUser([
            'fiscalNumber' => $this->secondUser->fiscal_number,
            'familyName' => $this->secondUser->family_name,
            'name' => $this->secondUser->name,
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
            ->json('GET', route('publicAdministrations.data.json'))
            ->assertOk()
            ->assertJsonFragment([
                'raw' => e($this->publicAdministration->name),
            ]);
    }

    /**
     * Test public administration user invitation successful.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     */
    public function testInviteRegisteredUserSuccesful(): void
    {
        $email = $this->faker->unique()->safeEmail;

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
                '_token' => 'test',
            ])
            ->post(route('users.store'), [
                '_token' => 'test',
                'email' => $email,
                'fiscal_number' => $this->secondUser->fiscal_number,
                'permissions' => [
                    $this->website->id => [
                        UserPermission::MANAGE_ANALYTICS,
                        UserPermission::READ_ANALYTICS,
                    ],
                ],
            ])
            ->assertSessionDoesntHaveErrors([
                'email',
                'fiscal_number',
                'is_admin',
                'permissions',
            ])
            ->assertRedirect(route('users.index'));

        Event::assertDispatched(UserInvited::class, function ($event) {
            return
                $this->secondUser->email === $event->getUser()->email
                && $this->publicAdministration->ipa_code === $event->getPublicAdministration()->ipa_code
                && $this->user->is($event->getInvitedBy());
        });

        User::findNotSuperAdminByFiscalNumber($this->secondUser->fiscal_number)->deleteAnalyticsServiceAccount();
    }

    /**
     * Test public administration user invitation fail.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     */
    public function testInviteRegisteredUserFailValidation(): void
    {
        $this->publicAdministration->users()->sync([$this->secondUser->id => ['user_email' => $this->secondUser->email, 'user_status' => UserStatus::ACTIVE]]);

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
                '_token' => 'test',
            ])
            ->post(route('users.store'), [
                '_token' => 'test',
                'email' => $this->secondUser->email,
                'fiscal_number' => $this->secondUser->fiscal_number,
                'permissions' => [
                    $this->website->id => [
                        UserPermission::MANAGE_ANALYTICS,
                        UserPermission::READ_ANALYTICS,
                    ],
                ],
            ])
            ->assertSessionDoesntHaveErrors([
                'email',
                'fiscal_number',
                'is_admin',
                'permissions',
            ]);

        Event::assertNotDispatched(UserInvited::class);
    }

    /**
     * Test website creation successful.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    public function testStoreWebsiteWhitPendingInviteSuccessful(): void
    {
        $this->publicAdministration->users()->sync([$this->secondUser->id => ['user_email' => $this->secondUser->email, 'user_status' => UserStatus::INVITED]], false);
        $alternativeEmail = $this->faker->unique()->safeEmail;

        $this->actingAs($this->secondUser)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.index'))
            ->post(route('websites.store.primary'), [
                'email' => $alternativeEmail,
                'public_administration_name' => 'Camera dei Deputati',
                'url' => 'www.camera.it',
                'ipa_code' => 'camera',
                'rtd_name' => 'Presidenza camera',
                'rtd_mail' => 'presidenza@camera.it',
                'correct_confirmation' => 'on',
                'skip_rtd_validation' => true,
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('websites.index'));

        $this->secondUser->refresh();

        $createdWebsite = Website::where('slug', Str::slug('www.camera.it'))->first();
        $this->app->make('analytics-service')->deleteSite($createdWebsite->analytics_id);

        Event::assertDispatched(PublicAdministrationRegistered::class, function ($event) {
            return 'camera' === $event->getPublicAdministration()->ipa_code && $this->secondUser->is($event->getUser());
        });
        Event::assertDispatched(WebsiteAdded::class, function ($event) {
            return $event->getWebsite()->slug === Str::slug('www.camera.it');
        });

        $this->assertTrue($this->secondUser->publicAdministrations()->where('ipa_code', 'camera')->get()->isNotEmpty());
    }

    /**
     * Test user confirmation of invitation.
     */
    public function testConfirmInvitationSuccess(): void
    {
        $this->publicAdministration->users()->sync([
            $this->secondUser->id => [
                'user_email' => $this->secondUser->email,
                'user_status' => UserStatus::INVITED,
            ],
        ], false);
        $this->actingAs($this->secondUser)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->json('POST', route('publicAdministration.activate', [
                'uuid' => $this->secondUser->uuid,
                'publicAdministration' => $this->publicAdministration->ipa_code,
            ]))
            ->assertOk()
            ->assertJsonFragment([
                'name' => e($this->publicAdministration->name),
            ]);
    }
}
