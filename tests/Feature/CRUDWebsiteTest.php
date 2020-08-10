<?php

namespace Tests\Feature;

use App\Enums\PublicAdministrationStatus;
use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WebsiteAccessType;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Events\PublicAdministration\PublicAdministrationRegistered;
use App\Events\User\UserWebsiteAccessChanged;
use App\Events\Website\WebsiteAdded;
use App\Events\Website\WebsiteUpdated;
use App\Http\Requests\StorePrimaryWebsiteRequest;
use App\Jobs\UpdateClosedBetaWhitelist;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use App\Services\MatomoService;
use Faker\Factory;
use Faker\Generator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Italia\SPIDAuth\SPIDUser;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/**
 * Website CRUD test.
 */
class CRUDWebsiteTest extends TestCase
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
     * The custom public administration.
     *
     * @var PublicAdministration the custom public administration
     */
    private $customPublicAdministration;

    /**
     * The custom website.
     *
     * @var Website the website for custom public administration
     */
    private $customWebsite;

    /**
     * Pre-test setup.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('wai.closed_beta', false);
        Event::fake();
        $this->user = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => Date::now(),
        ]);
        $this->publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $this->website = factory(Website::class)->create([
            'public_administration_id' => $this->publicAdministration->id,
            'status' => WebsiteStatus::ACTIVE,
            'type' => WebsiteType::INSTITUTIONAL,
        ]);
        $this->publicAdministration->users()->sync([$this->user->id => ['user_email' => $this->user->email, 'user_status' => UserStatus::ACTIVE]]);

        $this->spidUser = new SPIDUser([
            'fiscalNumber' => $this->user->fiscal_number,
            'familyName' => $this->user->family_name,
            'name' => $this->user->name,
        ]);

        $this->user->registerAnalyticsServiceAccount();

        $this->customPublicAdministration = factory(PublicAdministration::class)->create([
            'status' => PublicAdministrationStatus::PENDING,
        ]);
        $this->customWebsite = factory(Website::class)->create([
            'public_administration_id' => $this->customPublicAdministration->id,
            'status' => WebsiteStatus::PENDING,
            'type' => WebsiteType::INSTITUTIONAL_PLAY,
        ]);
        $this->customPublicAdministration->users()->sync([$this->user->id]);

        Bouncer::dontCache();
        Bouncer::scope()->onceTo($this->publicAdministration->id, function () {
            $this->user->assign(UserRole::ADMIN);
            $this->user->allow(UserPermission::MANAGE_ANALYTICS, $this->website);
            $this->user->allow(UserPermission::READ_ANALYTICS, $this->website);
            $this->user->allow(UserPermission::MANAGE_WEBSITES);
        });

        $this->faker = Factory::create();
    }

    /**
     * Post-test tear down.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    protected function tearDown(): void
    {
        $this->user->deleteAnalyticsServiceAccount();
        parent::tearDown();
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
            ])
            ->json('GET', route('websites.data.json'))
            ->assertOk()
            ->assertJsonFragment([
                'raw' => e($this->website->name),
            ]);
    }

    /**
     * Test primary website creation successful.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    public function testStorePrimaryWebsiteSuccessful(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::PENDING,
            'email_verified_at' => Date::now(),
        ]);
        $alternativeEmail = $this->faker->unique()->safeEmail;
        $this->actingAs($user)
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

        $user->refresh();

        $createdWebsite = Website::where('slug', Str::slug('www.camera.it'))->first();
        $this->app->make('analytics-service')->deleteSite($createdWebsite->analytics_id);

        Event::assertDispatched(PublicAdministrationRegistered::class, function ($event) use ($user) {
            return 'camera' === $event->getPublicAdministration()->ipa_code && $user->is($event->getUser());
        });
        Event::assertDispatched(WebsiteAdded::class, function ($event) {
            return $event->getWebsite()->slug === Str::slug('www.camera.it');
        });

        $this->assertEquals('camera', $user->publicAdministrations()->first()->ipa_code);
    }

    /**
     * Test primary website creation fail due to fields validation.
     */
    public function testStorePrimaryWebsiteFailValidation(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::PENDING,
            'email_verified_at' => Date::now(),
        ]);
        $this->actingAs($user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.index'))
            ->post(route('websites.store.primary'), [
                'url' => $this->website->url,
                'ipa_code' => $this->publicAdministration->ipa_code,
            ])
            ->assertSessionHasErrors([
                'public_administration_name',
                'url',
                'ipa_code',
                'correct_confirmation',
            ])
            ->assertRedirect(route('websites.index'));

        $user->refresh();

        Event::assertNotDispatched(PublicAdministrationRegistered::class);
        Event::assertNotDispatched(WebsiteAdded::class);

        $this->assertEmpty($user->publicAdministrations()->get());
    }

    /**
     * Test primary custom creation successful.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    public function testStoreCustomWebsiteSuccessful(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::PENDING,
            'email_verified_at' => Date::now(),
        ]);
        $this->actingAs($user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.create.primary.custom'))
            ->post(route('websites.store.primary'), [
                'public_administration_name' => 'Pubblica amministrazione personalizzata',
                'url' => 'https://www.pubblica-amministrazione.personalizzata.it',
                'city' => 'Roma',
                'county' => 'Roma',
                'region' => 'Lazio',
                'rtd_name' => 'Utente Utenti',
                'rtd_mail' => 'utente@personalizzata.it',
                'skip_rtd_validation' => true,
                'correct_confirmation' => 'on',
                'website_type' => 'custom',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('websites.index'));

        $user->refresh();

        $createdWebsite = Website::where('slug', Str::slug('https://www.pubblica-amministrazione.personalizzata.it'))->first();
        $this->app->make('analytics-service')->deleteSite($createdWebsite->analytics_id);

        Event::assertDispatched(PublicAdministrationRegistered::class, function ($event) use ($user) {
            return 'Pubblica amministrazione personalizzata' === $event->getPublicAdministration()->name && $user->is($event->getUser());
        });
        Event::assertDispatched(WebsiteAdded::class, function ($event) {
            return $event->getWebsite()->slug === Str::slug('https://www.pubblica-amministrazione.personalizzata.it');
        });

        $this->assertEquals('Pubblica amministrazione personalizzata', $user->publicAdministrations()->first()->name);
    }

    /**
     * Test custom website creation fail due to fields validation.
     */
    public function testStoreCustomWebsiteFailValidation(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::PENDING,
            'email_verified_at' => Date::now(),
        ]);
        $this->actingAs($user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.create.primary.custom'))
            ->post(route('websites.store.primary'), [
                'public_administration_name' => 'Pubblica amministrazione personalizzata',
                'url' => $this->website->url,
                'city' => 'Roma',
                'website_type' => 'custom',
            ])
            ->assertSessionHasErrors([
                'url',
                'county',
                'region',
            ])
            ->assertRedirect(route('websites.create.primary.custom'));

        $user->refresh();

        Event::assertNotDispatched(PublicAdministrationRegistered::class);
        Event::assertNotDispatched(WebsiteAdded::class);

        $this->assertEmpty($user->publicAdministrations()->get());
    }

    /**
     * Test primary custom creation successful.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    public function testForceActivationCustomWebsiteSuccessful(): void
    {
        $user = factory(User::class)->create([
            'status' => UserStatus::PENDING,
            'email_verified_at' => Date::now(),
        ]);

        $spidUser = new SPIDUser([
            'fiscalNumber' => $user->fiscal_number,
            'familyName' => $user->family_name,
            'name' => $user->name,
        ]);

        $user->registerAnalyticsServiceAccount();
        $this->customPublicAdministration->users()->sync([$user->id]);

        $this->assertTrue($this->customWebsite->status->is(WebsiteStatus::PENDING));

        $this->actingAs($user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $spidUser,
                'tenant_id' => $this->customPublicAdministration->id,
            ])
            ->from(route('websites.index'))
            ->json('GET', route('websites.tracking.force', ['website' => $this->customWebsite->slug]))
            ->assertOk()
            ->assertJsonFragment([
                'website_name' => e($this->customWebsite->name),
            ]);

        $user->refresh();
        $this->customWebsite->refresh();

        $this->assertTrue($this->customWebsite->status->is(WebsiteStatus::ACTIVE));
    }

    /**
     * Test website creation successful.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    public function testStoreWebsiteSuccessful(): void
    {
        $secondUser = factory(User::class)->create([
            'status' => UserStatus::INVITED,
        ]);
        $thirdUser = factory(User::class)->create([
            'status' => UserStatus::INVITED,
        ]);
        $this->publicAdministration->users()->sync([$secondUser->id => ['user_email' => $secondUser->email, 'user_status' => UserStatus::INVITED], $thirdUser->id => ['user_email' => $thirdUser->email, 'user_status' => UserStatus::INVITED]], false);

        $secondUser->registerAnalyticsServiceAccount();
        $thirdUser->registerAnalyticsServiceAccount();

        Bouncer::scope()->onceTo($this->publicAdministration->id, function () use ($secondUser, $thirdUser) {
            $secondUser->assign(UserRole::DELEGATED);
            $thirdUser->assign(UserRole::DELEGATED);
        });

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.create'))
            ->post(route('websites.store'), [
                'website_name' => 'Sito tematico',
                'url' => 'https://www.test.local',
                'type' => WebsiteType::INFORMATIONAL,
                'permissions' => [
                    $secondUser->id => [
                        UserPermission::READ_ANALYTICS,
                    ],
                ],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('websites.index'));

        $secondUser->deleteAnalyticsServiceAccount();
        $thirdUser->deleteAnalyticsServiceAccount();

        $createdWebsite = Website::where('slug', Str::slug('https://www.test.local'))->first();
        $this->app->make('analytics-service')->deleteSite($createdWebsite->analytics_id);

        Event::assertDispatched(WebsiteAdded::class, function ($event) {
            return $event->getWebsite()->slug === Str::slug('https://www.test.local');
        });

        Event::assertDispatched(UserWebsiteAccessChanged::class, function ($event) {
            return $this->user->is($event->getUser())
                && Str::slug('https://www.test.local') === $event->getWebsite()->slug
                && $event->getAccessType()->is(WebsiteAccessType::WRITE);
        });

        Event::assertDispatched(UserWebsiteAccessChanged::class, function ($event) use ($secondUser) {
            return $secondUser->is($event->getUser())
                && Str::slug('https://www.test.local') === $event->getWebsite()->slug
                && $event->getAccessType()->is(WebsiteAccessType::VIEW);
        });

        Event::assertDispatched(UserWebsiteAccessChanged::class, function ($event) use ($thirdUser) {
            return $thirdUser->is($event->getUser())
                && Str::slug('https://www.test.local') === $event->getWebsite()->slug
                && $event->getAccessType()->is(WebsiteAccessType::NO_ACCESS);
        });
    }

    /**
     * Test website creation fail due to fields validation.
     */
    public function testStoreWebsiteFailValidation(): void
    {
        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.create'))
            ->post(route('websites.store'), [
                'url' => 'www.camera.it',
                'type' => WebsiteType::INSTITUTIONAL,
            ])
            ->assertSessionHasErrors([
                'website_name',
                'url',
                'type',
            ])
            ->assertRedirect(route('websites.create'));

        Event::assertNotDispatched(WebsiteAdded::class);
        Event::assertNotDispatched(UserWebsiteAccessChanged::class);
    }

    /**
     * Test website update successful.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    public function testUpdateWebsiteSuccessful(): void
    {
        $secondUser = factory(User::class)->create([
            'status' => UserStatus::INVITED,
        ]);
        $thirdUser = factory(User::class)->create([
            'status' => UserStatus::INVITED,
        ]);
        $this->publicAdministration->users()->sync([$secondUser->id, $thirdUser->id], false);

        $secondUser->registerAnalyticsServiceAccount();
        $thirdUser->registerAnalyticsServiceAccount();

        $secondUser->assign(UserRole::DELEGATED);
        $thirdUser->assign(UserRole::DELEGATED);

        do {
            $secondWebsite = factory(Website::class)->make([
                'type' => WebsiteType::MOBILE,
                'public_administration_id' => $this->publicAdministration->id,
            ]);
        } while ($secondWebsite->slug === $this->website->slug);

        $analyticsId = app()->make('analytics-service')->registerSite($secondWebsite->name . ' [' . $secondWebsite->type->value . ']', $secondWebsite->url, $this->publicAdministration->name);
        $secondWebsite->analytics_id = $analyticsId;
        $secondWebsite->save();

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.edit', ['website' => $secondWebsite->slug]))
            ->put(route('websites.update', ['website' => $secondWebsite->slug]), [
                'website_name' => 'Nuovo nome sito tematico',
                'url' => 'https://www.test.local',
                'type' => WebsiteType::INFORMATIONAL,
                'permissions' => [
                    $secondUser->id => [
                        UserPermission::READ_ANALYTICS,
                    ],
                ],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('websites.index'));

        $secondUser->deleteAnalyticsServiceAccount();
        $thirdUser->deleteAnalyticsServiceAccount();
        $this->app->make('analytics-service')->deleteSite($secondWebsite->analytics_id);

        Event::assertDispatched(WebsiteUpdated::class, function ($event) {
            $website = $event->getWebsite();

            return Str::slug('https://www.test.local') === $website->slug
                && 'Nuovo nome sito tematico' === $website->name
                && WebsiteType::INFORMATIONAL === $website->type->value;
        });

        Event::assertDispatched(UserWebsiteAccessChanged::class, function ($event) use ($secondUser) {
            return $secondUser->is($event->getUser())
                && Str::slug('https://www.test.local') === $event->getWebsite()->slug
                && $event->getAccessType()->is(WebsiteAccessType::VIEW);
        });

        Event::assertDispatched(UserWebsiteAccessChanged::class, function ($event) use ($thirdUser) {
            return $thirdUser->is($event->getUser())
                && Str::slug('https://www.test.local') === $event->getWebsite()->slug
                && $event->getAccessType()->is(WebsiteAccessType::NO_ACCESS);
        });
    }

    /**
     * Test website update fail due to fields validation.
     */
    public function testUpdateWebsiteFailValidation(): void
    {
        do {
            $secondWebsite = factory(Website::class)->make([
                'type' => WebsiteType::MOBILE,
                'public_administration_id' => $this->publicAdministration->id,
            ]);
        } while ($this->website->slug === $secondWebsite->slug);
        $secondWebsite->save();

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.edit', ['website' => $secondWebsite->slug]))
            ->put(route('websites.update', ['website' => $secondWebsite->slug]), [
                'url' => 'www.camera.it',
                'type' => WebsiteType::INSTITUTIONAL,
            ])
            ->assertSessionHasErrors([
                'website_name',
                'url',
                'type',
            ])
            ->assertRedirect(route('websites.edit', ['website' => $secondWebsite->slug]));

        Event::assertNotDispatched(WebsiteUpdated::class);
    }

    /**
     * Test website update fail due to last website enabled validation rule.
     */
    public function testUpdateWebsiteFailLastWebsiteEnabledValidation(): void
    {
        $secondUser = factory(User::class)->create([
            'status' => UserStatus::INVITED,
        ]);
        $this->publicAdministration->users()->sync([$secondUser->id], false);

        $secondUser->registerAnalyticsServiceAccount();

        $secondUser->assign(UserRole::DELEGATED);
        Bouncer::allow($secondUser)->to(UserPermission::READ_ANALYTICS, $this->website);
        Bouncer::allow($secondUser)->to(UserPermission::MANAGE_ANALYTICS, $this->website);
        Bouncer::disallow($secondUser)->to(UserPermission::NO_ACCESS, $this->website);

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.edit', ['website' => $this->website->slug]))
            ->put(route('websites.update', ['website' => $this->website->slug]), [
                'website_name' => $this->website->name,
                'url' => $this->website->url,
                'type' => $this->website->type->description,
            ])
            ->assertSessionHasErrors([
                'permissions',
            ])
            ->assertRedirect(route('websites.edit', ['website' => $this->website->slug]));

        Event::assertNotDispatched(WebsiteUpdated::class);
        Event::assertNotDispatched(UserWebsiteAccessChanged::class);
    }

    /**
     * Test primary website update successful.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    public function testUpdatePrimaryWebsiteSuccessful(): void
    {
        $secondUser = factory(User::class)->create([
            'status' => UserStatus::INVITED,
        ]);
        $thirdUser = factory(User::class)->create([
            'status' => UserStatus::INVITED,
        ]);
        $this->publicAdministration->users()->sync([$secondUser->id, $thirdUser->id], false);

        $secondUser->registerAnalyticsServiceAccount();
        $thirdUser->registerAnalyticsServiceAccount();

        $secondUser->assign(UserRole::DELEGATED);
        $thirdUser->assign(UserRole::DELEGATED);

        Event::fakeFor(function () {
            $analyticsId = app()->make('analytics-service')->registerSite($this->website->name . ' [' . $this->website->type->value . ']', $this->website->url, $this->publicAdministration->name);
            $this->website->analytics_id = $analyticsId;
            $this->website->save();
        });

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.edit', ['website' => $this->website->slug]))
            ->put(route('websites.update', ['website' => $this->website->slug]), [
                'website_name' => $this->website->name,
                'url' => $this->website->url,
                'type' => WebsiteType::getDescription(WebsiteType::INSTITUTIONAL),
                'permissions' => [
                    $secondUser->id => [
                        UserPermission::READ_ANALYTICS,
                    ],
                ],
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('websites.index'));

        $secondUser->deleteAnalyticsServiceAccount();
        $thirdUser->deleteAnalyticsServiceAccount();
        $this->app->make('analytics-service')->deleteSite($this->website->analytics_id);

        Event::assertNotDispatched(WebsiteUpdated::class);

        Event::assertDispatched(UserWebsiteAccessChanged::class, function ($event) use ($secondUser) {
            return $secondUser->is($event->getUser())
                && $this->website->slug === $event->getWebsite()->slug
                && $event->getAccessType()->is(WebsiteAccessType::VIEW);
        });

        Event::assertDispatched(UserWebsiteAccessChanged::class, function ($event) use ($thirdUser) {
            return $thirdUser->is($event->getUser())
                && $this->website->slug === $event->getWebsite()->slug
                && $event->getAccessType()->is(WebsiteAccessType::NO_ACCESS);
        });
    }

    /**
     * Test primary website update fail due to fields validation.
     */
    public function testUpdatePrimaryWebsiteFailValidation(): void
    {
        $secondUser = factory(User::class)->create([
            'status' => UserStatus::INVITED,
        ]);
        $this->publicAdministration->users()->sync([$secondUser->id], false);

        $secondUser->registerAnalyticsServiceAccount();

        $secondUser->assign(UserRole::DELEGATED);

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.edit', ['website' => $this->website->slug]))
            ->put(route('websites.update', ['website' => $this->website->slug]), [
                'url' => 'https://www.test.local',
                'type' => WebsiteType::INFORMATIONAL,
                'permissions' => [
                    $secondUser->id => [
                        UserPermission::READ_ANALYTICS,
                    ],
                ],
            ])
            ->assertSessionHasErrors([
                'website_name',
                'url',
                'type',
            ])
            ->assertRedirect(route('websites.edit', ['website' => $this->website->slug]));

        Event::assertNotDispatched(WebsiteUpdated::class);
        Event::assertNotDispatched(UserWebsiteAccessChanged::class);
    }

    /**
     * Test primary website successfully registered with closed beta.
     */
    public function testStorePrimaryWebsiteSuccessfulClosedBeta(): void
    {
        Config::set('wai.closed_beta', true);
        Cache::shouldReceive('rememberForever')
            ->withSomeOfArgs(UpdateClosedBetaWhitelist::CLOSED_BETA_WHITELIST_KEY)
            ->andReturn(collect(['fake']));

        $this->app->bind(StorePrimaryWebsiteRequest::class, function () {
            return $this->partialMock(StorePrimaryWebsiteRequest::class, function ($mock) {
                $mock->shouldReceive('getPublicAdministrationEntryByIpaCode')
                    ->withArgs(['fake'])
                    ->once()
                    ->andReturn([
                        'ipa_code' => 'fake',
                        'name' => 'Fake name',
                        'pec' => 'pec@example.local',
                        'site' => 'www.example.local',
                        'rtd_name' => null,
                        'rtd_mail' => null,
                        'rtd_pec' => null,
                        'city' => 'Roma',
                        'county' => 'RM',
                        'region' => 'Lazio',
                        'type' => 'Fake type',
                    ]);
            });
        });

        $user = factory(User::class)->create([
            'status' => UserStatus::PENDING,
            'email_verified_at' => Date::now(),
        ]);
        $alternativeEmail = $this->faker->unique()->safeEmail;

        $this->app->bind('analytics-service', function () use ($user) {
            return $this->partialMock(MatomoService::class, function ($mock) use ($user) {
                $mock->shouldReceive('registerSite')
                    ->withArgs([
                        __('Sito istituzionale'),
                        'www.example.local',
                        'Fake name',
                    ])
                    ->andReturn(1);
                $mock->shouldReceive('registerUser')
                    ->withArgs([
                        $user->uuid,
                        $user->analytics_password,
                        $user->email,
                    ])
                    ->andReturn();
                $mock->shouldReceive('setWebsiteAccess')
                    ->withArgs([
                        $user->uuid,
                        WebsiteAccessType::VIEW,
                        1,
                    ])
                    ->andReturn();
            });
        });

        $this->actingAs($user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.index'))
            ->post(route('websites.store.primary'), [
                'email' => $alternativeEmail,
                'public_administration_name' => 'PA Test',
                'url' => 'www.example.local',
                'ipa_code' => 'fake',
                'correct_confirmation' => 'on',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('websites.index'))
            ->assertSessionHas([
                'modal' => [
                    'title' => __('Il sito è stato inserito, adesso procedi ad attivarlo!'),
                    'icon' => 'it-check-circle',
                    'message' => __('Abbiamo inviato al tuo indirizzo email le istruzioni per attivare il sito e iniziare a monitorare il traffico.'),
                    'image' => asset('images/primary-website-added.svg'),
                ],
            ]);

        Event::assertDispatched(PublicAdministrationRegistered::class, function ($event) use ($user) {
            return 'fake' === $event->getPublicAdministration()->ipa_code && $user->is($event->getUser());
        });
        Event::assertDispatched(WebsiteAdded::class, function ($event) {
            return $event->getWebsite()->slug === Str::slug('www.example.local');
        });

        //NOTE: rebind real analytics service.
        //      To be removed when full tests refactoring is completed
        $this->app->bind('analytics-service', function () {
            return new MatomoService();
        });
    }

    /**
     * Test primary website fail registration with closed beta due to ipa code not whitelisted.
     */
    public function testStorePrimaryWebsiteFailValidationClosedBeta(): void
    {
        Config::set('wai.closed_beta', true);
        Cache::shouldReceive('rememberForever')
            ->withSomeOfArgs(UpdateClosedBetaWhitelist::CLOSED_BETA_WHITELIST_KEY)
            ->andReturn(collect(['another-pa']));

        $this->app->bind(StorePrimaryWebsiteRequest::class, function () {
            return $this->partialMock(StorePrimaryWebsiteRequest::class, function ($mock) {
                $mock->shouldReceive('getPublicAdministrationEntryByIpaCode')
                    ->withArgs(['fake'])
                    ->once()
                    ->andReturn(['ipa_code' => 'fake']);
            });
        });

        $user = factory(User::class)->create([
            'status' => UserStatus::PENDING,
            'email_verified_at' => Date::now(),
        ]);
        $this->actingAs($user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.index'))
            ->post(route('websites.store.primary'), [
                'public_administration_name' => 'PA Test',
                'url' => 'www.example.local',
                'ipa_code' => 'fake',
                'correct_confirmation' => 'on',
            ])
            ->assertSessionHasErrors([
                'public_administration_name' => __('PA non inclusa in fase di beta chiusa'),
            ])
            ->assertRedirect(route('websites.index'))
            ->assertSessionHas([
                'modal' => [
                    'title' => __('Accesso limitato'),
                    'icon' => 'it-close-circle',
                    'message' => implode("\n", [
                        __(':app è in una fase di beta chiusa (:closed-beta-faq).', [
                            'app' => '<strong>' . config('app.name') . '</strong>',
                            'closed-beta-faq' => '<a href="' . route('faq') . '#beta-chiusa">' . __('cosa significa?') . '</a>',
                        ]),
                        __("Durante questa fase sperimentale, l'accesso è limitato ad un numero chiuso di pubbliche amministrazioni pilota."),
                    ]),
                    'image' => asset('images/closed.svg'),
                ],
            ]);

        Event::assertNotDispatched(PublicAdministrationRegistered::class);
        Event::assertNotDispatched(WebsiteAdded::class);
    }
}
