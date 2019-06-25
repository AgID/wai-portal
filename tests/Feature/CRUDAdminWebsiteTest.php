<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Enums\WebsiteType;
use App\Events\Website\WebsiteDeleted;
use App\Events\Website\WebsiteRestored;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class CRUDAdminWebsiteTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $publicAdministration;

    private $website;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();

        $this->publicAdministration = factory(PublicAdministration::class)
            ->state('active')
            ->create();
        $this->website = factory(Website::class)->make([
            'public_administration_id' => $this->publicAdministration->id,
            'type' => WebsiteType::TESTING,
        ]);
        $analyticsId = $this->app->make('analytics-service')->registerSite($this->website->name . ' [' . $this->website->type->value . ']', $this->website->url, $this->publicAdministration->name);
        $this->website->analytics_id = $analyticsId;
        $this->website->save();

        $this->user = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => Date::now(),
        ]);

        Bouncer::dontCache();
        Bouncer::scope()->to(0);
        $this->user->assign(UserRole::SUPER_ADMIN);
        $this->user->allow(UserPermission::MANAGE_WEBSITES);
        $this->user->allow(UserPermission::ACCESS_ADMIN_AREA);
    }

    public function tearDown(): void
    {
        $this->app->make('analytics-service')->deleteSite($this->website->analytics_id);
        parent::tearDown();
    }

    public function testPublicAdministrationWebsiteDatatableData(): void
    {
        $this->actingAs($this->user)
            ->json(
                'GET',
                route('admin.publicAdministration.websites.data.json', ['publicAdministration' => $this->publicAdministration]),
                )
            ->assertOk()
            ->assertJsonFragment([
                'name' => $this->website->name,
            ]);
    }

    public function testDeleteWebsiteSuccessful(): void
    {
        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.websites.delete', ['publicAdministration' => $this->publicAdministration, 'website' => $this->website]))
            ->assertOk();

        $this->website->refresh();

        $this->assertTrue($this->website->trashed());

        Event::assertDispatched(WebsiteDeleted::class, function ($event) {
            return $this->website->slug === $event->getWebsite()->slug;
        });
    }

    public function testDeleteWebsiteFailPrimary(): void
    {
        Event::fakeFor(function () {
            $this->website->type = WebsiteType::PRIMARY;
            $this->website->save();
        });

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.websites.delete', ['publicAdministration' => $this->publicAdministration, 'website' => $this->website]))
            ->assertStatus(400)
            ->assertJson([
                'result' => 'error',
                'message' => 'Impossibile eliminare un sito istituzionale',
            ]);

        $this->website->refresh();

        $this->assertFalse($this->website->trashed());

        Event::assertNotDispatched(WebsiteDeleted::class);
    }

    public function testRestoreWebsiteSuccessful(): void
    {
        Event::fakeFor(function () {
            $this->website->delete();
        });

        $this->actingAs($this->user)
            ->patch(
                route('admin.publicAdministration.websites.restore', ['publicAdministration' => $this->publicAdministration, 'website' => $this->website]))
            ->assertOk();

        $this->website->refresh();

        $this->assertFalse($this->website->trashed());

        Event::assertDispatched(WebsiteRestored::class, function ($event) {
            return $this->website->slug === $event->getWebsite()->slug;
        });
    }
}
