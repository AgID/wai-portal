<?php

namespace Tests\Unit;

use App\Enums\WebsiteAccessType;
use App\Enums\WebsiteType;
use App\Events\Jobs\PendingWebsitesCheckCompleted;
use App\Events\PublicAdministration\PublicAdministrationActivated;
use App\Events\PublicAdministration\PublicAdministrationPurged;
use App\Events\User\UserActivated;
use App\Events\User\UserWebsiteAccessChanged;
use App\Events\Website\WebsitePurging;
use App\Exceptions\CommandErrorException;
use App\Jobs\ProcessPendingWebsites;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use GuzzleHttp\Client as TrackingClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/**
 * Pending websites check job test.
 */
class ProcessPendingWebsitesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    /**
     * Test job complete successfully.
     */
    public function testCheckWebsitesCompleted(): void
    {
        $job = new ProcessPendingWebsites();
        $job->handle();

        Event::assertDispatched(PendingWebsitesCheckCompleted::class);
    }

    /**
     * Test job complete with purged website.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     */
    public function testCheckWebsitePurged(): void
    {
        $user = factory(User::class)->state('pending')->create();
        $publicAdministration = factory(PublicAdministration::class)->create();
        $publicAdministration->users()->sync($user->id);
        $website = factory(Website::class)->create([
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subDays((int) config('wai.purge_expiry') + 1),
        ]);

        $this->app->make('analytics-service')->registerUser($user->uuid, $user->analytics_password, $user->email, config('analytics-service.admin_token'));
        $siteID = $this->app->make('analytics-service')->registerSite($website->name, $website->url, $publicAdministration->name);
        $website->analytics_id = $siteID;
        $website->save();

        $job = new ProcessPendingWebsites();
        $job->handle();

        Event::assertDispatched(PublicAdministrationPurged::class, function ($event) use ($publicAdministration) {
            return json_decode($event->getPublicAdministrationJson())->ipa_code === $publicAdministration->ipa_code;
        });

        $purgedUser = User::findByFiscalNumber($user->fiscalNumber);
        $this->assertNull($purgedUser->partial_analytics_password);

        $this->expectException(CommandErrorException::class);
        $this->app->make('analytics-service')->getUserByEmail($purgedUser->email, config('analytics-service.admin_token'));

        Event::assertDispatched(PendingWebsitesCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getPurged(), true)
                && empty($event->getPurging())
                && empty($event->getActivated())
                && empty($event->getFailed());
        });

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }

    /**
     * Test job complete with near-to-be-purged website.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     */
    public function testCheckWebsitePurging(): void
    {
        $user = factory(User::class)->state('pending')->create();
        $publicAdministration = factory(PublicAdministration::class)->create();
        $publicAdministration->users()->sync($user->id);
        $website = factory(Website::class)->create([
            'public_administration_id' => $publicAdministration->id,
            'created_at' => now()->subDays((int) config('wai.purge_warning')),
        ]);

        $siteID = $this->app->make('analytics-service')->registerSite($website->name, $website->url, $publicAdministration->name);
        $website->analytics_id = $siteID;
        $website->save();

        $job = new ProcessPendingWebsites();
        $job->handle();

        $this->app->make('analytics-service')->deleteSite($website->analytics_id, config('analytics-service.admin_token'));

        Event::assertDispatched(WebsitePurging::class, function ($event) use ($website) {
            return $event->getWebsite()->id === $website->id;
        });

        Event::assertDispatched(PendingWebsitesCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getPurging(), true)
                && empty($event->getPurged())
                && empty($event->getActivated())
                && empty($event->getFailed());
        });
    }

    /**
     * Test job complete with primary website for public administration activated.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \GuzzleHttp\Exception\GuzzleException if unable to inject tracking request
     */
    public function testCheckWebsitePrimaryActivated(): void
    {
        $user = factory(User::class)->state('pending')->create();
        $publicAdministration = factory(PublicAdministration::class)->create();
        $publicAdministration->users()->sync($user->id);
        $website = factory(Website::class)->make([
            'type' => WebsiteType::PRIMARY,
            'public_administration_id' => $publicAdministration->id,
        ]);

        $tokenAuth = config('analytics-service.admin_token');
        $this->app->make('analytics-service')->registerUser($user->uuid, $user->analytics_password, $user->email, $tokenAuth);
        $analyticsId = app()->make('analytics-service')->registerSite('Sito istituzionale', $website->url, $website->publicAdministration->name);
        $website->analytics_id = $analyticsId;
        $website->save();

        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $analyticsId,
            ],
            'verify' => false,
        ]);

        $job = new ProcessPendingWebsites();
        $job->handle();

        $this->app->make('analytics-service')->deleteUser($user->uuid, config('analytics-service.admin_token'));
        $this->app->make('analytics-service')->deleteSite($website->analytics_id, config('analytics-service.admin_token'));

        Event::assertDispatched(UserActivated::class, function ($event) use ($user) {
            return $event->getUser()->id === $user->id;
        });

        Event::assertDispatched(PublicAdministrationActivated::class, function ($event) use ($publicAdministration) {
            return $event->getPublicAdministration()->ipa_code === $publicAdministration->ipa_code;
        });

        Event::assertDispatched(PendingWebsitesCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug], $event->getActivated(), true)
                && empty($event->getPurged())
                && empty($event->getPurging())
                && empty($event->getFailed());
        });

        Bouncer::scope()->to($publicAdministration->id);
        $this->assertTrue($user->isAn('admin'));
    }

    /**
     * Test job complete with secondary website activated.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \App\Exceptions\CommandErrorException if command finishes with error
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to the service
     * @throws \GuzzleHttp\Exception\GuzzleException if unable to inject tracking request
     */
    public function testCheckWebsiteSecondaryActivated(): void
    {
        $userAdmin = factory(User::class)->state('active')->create();
        $userWrite = factory(User::class)->state('active')->create();
        $userView = factory(User::class)->state('active')->create();
        $userNoAccess = factory(User::class)->state('active')->create();
        $publicAdministration = factory(PublicAdministration::class)->state('active')->create();
        $publicAdministration->users()->sync([$userAdmin->id, $userWrite->id, $userView->id, $userNoAccess->id]);
        $website = factory(Website::class)->make([
            'type' => WebsiteType::SECONDARY,
            'public_administration_id' => $publicAdministration->id,
        ]);

        $this->app->make('analytics-service')->registerUser($userAdmin->uuid, $userAdmin->analytics_password, $userAdmin->email, config('analytics-service.admin_token'));
        $this->app->make('analytics-service')->registerUser($userWrite->uuid, $userWrite->analytics_password, $userWrite->email, config('analytics-service.admin_token'));
        $this->app->make('analytics-service')->registerUser($userView->uuid, $userView->analytics_password, $userView->email, config('analytics-service.admin_token'));
        $this->app->make('analytics-service')->registerUser($userNoAccess->uuid, $userNoAccess->analytics_password, $userNoAccess->email, config('analytics-service.admin_token'));
        $analyticsId = app()->make('analytics-service')->registerSite('Sito istituzionale', $website->url, $website->publicAdministration->name);
        $website->analytics_id = $analyticsId;
        $website->save();

        Bouncer::scope()->to($publicAdministration->id);
        session()->put('tenant_id', $publicAdministration->id);
        $userAdmin->assign('admin');
        $userWrite->setWriteAccessForWebsite($website);
        $userView->setViewAccessForWebsite($website);
        $userNoAccess->setNoAccessForWebsite($website);

        $client = new TrackingClient(['base_uri' => config('analytics-service.api_base_uri')]);
        $client->request('GET', 'piwik.php', [
            'query' => [
                'rec' => '1',
                'idsite' => $analyticsId,
            ],
            'verify' => false,
        ]);

        $job = new ProcessPendingWebsites();
        $job->handle();

        $this->app->make('analytics-service')->deleteUser($userAdmin->uuid, config('analytics-service.admin_token'));
        $this->app->make('analytics-service')->deleteUser($userWrite->uuid, config('analytics-service.admin_token'));
        $this->app->make('analytics-service')->deleteUser($userView->uuid, config('analytics-service.admin_token'));
        $this->app->make('analytics-service')->deleteUser($userNoAccess->uuid, config('analytics-service.admin_token'));
        $this->app->make('analytics-service')->deleteSite($website->analytics_id, config('analytics-service.admin_token'));

        Event::assertDispatched(UserWebsiteAccessChanged::class, function ($event) use ($userAdmin, $userWrite, $userView, $userNoAccess, $website) {
            logger($event->getUser()->getInfo() . ' ' . $event->getWebsite()->getInfo() . ' ' . $event->getAccessType()->description);

            return ($event->getUser()->id === $userAdmin->id && $event->getWebsite()->id === $website->id && $event->getAccessType()->is(WebsiteAccessType::ADMIN))
                || ($event->getUser()->id === $userWrite->id && $event->getWebsite()->id === $website->id && $event->getAccessType()->is(WebsiteAccessType::WRITE))
                || ($event->getUser()->id === $userView->id && $event->getWebsite()->id === $website->id && $event->getAccessType()->is(WebsiteAccessType::VIEW))
                || ($event->getUser()->id === $userNoAccess->id && $event->getWebsite()->id === $website->id && $event->getAccessType()->is(WebsiteAccessType::NO_ACCESS));
        });
    }

    /**
     * Test job complete with failed website due to missing website into Analytics Service.
     */
    public function testMissingAnalyticsWebsiteFail(): void
    {
        $user = factory(User::class)->state('pending')->create();
        $publicAdministration = factory(PublicAdministration::class)->create();
        $publicAdministration->users()->sync($user->id);
        $website = factory(Website::class)->create([
            'type' => WebsiteType::PRIMARY,
            'public_administration_id' => $publicAdministration->id,
        ]);

        $job = new ProcessPendingWebsites();
        $job->handle();

        Event::assertDispatched(PendingWebsitesCheckCompleted::class, function ($event) use ($website) {
            return in_array(['website' => $website->slug, 'reason' => 'Invalid command for Analytics Service'], $event->getFailed(), true)
                && empty($event->getPurged())
                && empty($event->getPurging())
                && empty($event->getActivated());
        });
    }
}
