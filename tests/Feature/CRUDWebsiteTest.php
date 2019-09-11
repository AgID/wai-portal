<?php

namespace Tests\Feature;

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
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
     * Pre-test setup.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
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
        $this->website = factory(Website::class)->create([
            'public_administration_id' => $this->publicAdministration->id,
            'status' => WebsiteStatus::ACTIVE,
            'type' => WebsiteType::PRIMARY,
        ]);
        $this->publicAdministration->users()->sync([$this->user->id]);

        $this->spidUser = new SPIDUser([
            'fiscalNumber' => $this->user->fiscal_number,
            'familyName' => $this->user->family_name,
            'name' => $this->user->name,
        ]);

        $this->user->registerAnalyticsServiceAccount();

        Bouncer::dontCache();
        Bouncer::scope()->to($this->publicAdministration->id);
        $this->user->assign(UserRole::ADMIN);
        $this->user->allow(UserPermission::MANAGE_ANALYTICS, $this->website);
        $this->user->allow(UserPermission::READ_ANALYTICS, $this->website);
        $this->user->allow(UserPermission::MANAGE_WEBSITES);
    }

    /**
     * Post-test tear down.
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command is unsuccessful
     */
    public function tearDown(): void
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
                'raw' => $this->website->name,
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
        $this->actingAs($user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.index'))
            ->post(route('websites.store.primary'), [
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
        $this->publicAdministration->users()->sync([$secondUser->id, $thirdUser->id], false);

        $secondUser->registerAnalyticsServiceAccount();
        $thirdUser->registerAnalyticsServiceAccount();

        $secondUser->assign(UserRole::DELEGATED);
        $thirdUser->assign(UserRole::DELEGATED);

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
                'spid_user' => $this->spidUser,
            ])
            ->from(route('websites.create'))
            ->post(route('websites.store'), [
                'website_name' => 'Sito secondario',
                'url' => 'https://www.test.local',
                'type' => WebsiteType::TESTING,
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
                'type' => WebsiteType::PRIMARY,
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
                'type' => WebsiteType::WEBAPP,
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
                'website_name' => 'Nuovo nome sito secondario',
                'url' => 'https://www.test.local',
                'type' => WebsiteType::TESTING,
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
                && 'Nuovo nome sito secondario' === $website->name
                && WebsiteType::TESTING === $website->type->value;
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
                'type' => WebsiteType::WEBAPP,
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
                'type' => WebsiteType::PRIMARY,
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
                'type' => WebsiteType::getDescription(WebsiteType::PRIMARY),
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
                'type' => WebsiteType::TESTING,
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
}
