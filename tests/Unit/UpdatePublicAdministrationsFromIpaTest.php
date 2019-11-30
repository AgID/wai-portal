<?php

namespace Tests\Unit;

use App\Enums\PublicAdministrationStatus;
use App\Enums\WebsiteType;
use App\Events\Jobs\PublicAdministrationsUpdateFromIpaCompleted;
use App\Events\PublicAdministration\PublicAdministrationPrimaryWebsiteUpdated;
use App\Events\PublicAdministration\PublicAdministrationUpdated;
use App\Jobs\ProcessPublicAdministrationsUpdateFromIpa;
use App\Models\PublicAdministration;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Update public administrations from IPA job tests.
 */
class UpdatePublicAdministrationsFromIpaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test registered Public Administration check with no updates.
     */
    public function testNoPublicAdministrationUpdate(): void
    {
        Event::fake();

        $public_administration = factory(PublicAdministration::class)->create([
            'ipa_code' => 'camera',
            'name' => 'Camera dei Deputati',
            'pec' => 'camera_protcentrale@certcamera.it',
            'city' => 'Roma',
            'county' => 'RM',
            'region' => 'Lazio',
            'type' => 'Organi Costituzionali e di Rilievo Costituzionale',
            'status' => PublicAdministrationStatus::ACTIVE,
        ]);

        factory(Website::class)->create([
            'url' => 'www.camera.it',
            'slug' => Str::slug('www.camera.it'),
            'public_administration_id' => $public_administration->id,
        ]);

        $job = new ProcessPublicAdministrationsUpdateFromIpa();
        $job->handle();

        Event::assertDispatched(PublicAdministrationsUpdateFromIpaCompleted::class);
        Event::assertNotDispatched(PublicAdministrationUpdated::class);
        Event::assertNotDispatched(PublicAdministrationPrimaryWebsiteUpdated::class);
    }

    /**
     * Test registered Public Administration check with updates.
     */
    public function testPublicAdministrationUpdate(): void
    {
        Event::fake();

        $public_administration = factory(PublicAdministration::class)->create([
            'ipa_code' => 'camera',
            'name' => 'Camera dei Deputati',
            'pec' => 'camera_protcentrale@certcamera.it',
            'city' => 'Firenze',
            'county' => 'RM',
            'region' => 'Lazio',
            'type' => 'Organi Costituzionali e di Rilievo Costituzionale',
            'status' => PublicAdministrationStatus::ACTIVE,
        ]);

        $website = factory(Website::class)->create([
            'url' => 'www.camera.com',
            'slug' => Str::slug('www.camera.com'),
            'public_administration_id' => $public_administration->id,
            'type' => WebsiteType::PRIMARY,
        ]);

        $job = new ProcessPublicAdministrationsUpdateFromIpa();
        $job->handle();

        Event::assertDispatched(PublicAdministrationsUpdateFromIpaCompleted::class);
        Event::assertDispatched(PublicAdministrationPrimaryWebsiteUpdated::class, function ($event) use ($public_administration, $website) {
            return $event->getPublicAdministration()->ipa_code === $public_administration->ipa_code && $event->getPrimaryWebsite()->url === $website->url && 'www.camera.it' === $event->getNewURL();
        });

        Event::assertDispatched(PublicAdministrationUpdated::class, function ($event) use ($public_administration, $website) {
            $updated_pa = $event->getPublicAdministration();
            $expected_updates = [
                'city' => [
                    'old' => $public_administration->city,
                    'new' => 'Roma',
                ],
                'site' => [
                    'old' => $website->url,
                    'new' => 'www.camera.it',
                ],
            ];

            return $updated_pa->ipa_code === $public_administration->ipa_code && $event->getUpdates() === $expected_updates;
        });
    }
}
