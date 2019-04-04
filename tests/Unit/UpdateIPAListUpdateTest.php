<?php

namespace Tests\Unit;

use App\Enums\PublicAdministrationStatus;
use App\Enums\WebsiteType;
use App\Events\Jobs\IPAUpdateCompleted;
use App\Events\Jobs\IPAUpdateFailed;
use App\Events\PublicAdministration\PublicAdministrationUpdated;
use App\Events\PublicAdministration\PublicAdministrationWebsiteUpdated;
use App\Jobs\ProcessIPAList;
use App\Models\PublicAdministration;
use App\Models\Website;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Update IPA job tests.
 */
class UpdateIPAListUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $filename = 'ipa_csv/ipa_csv_' . Carbon::now()->toDateString() . '.csv';

        if (Storage::exists($filename) && !Storage::exists($filename . '.original')) {
            Storage::move($filename, $filename . '.original');
        }
        $file = File::get(storage_path('testing/amministrazioni.csv'));
        Storage::put($filename, $file);
    }

    /**
     * Clean up the testing environment before the next test.
     */
    protected function tearDown(): void
    {
        $filename = 'ipa_csv/ipa_csv_' . Carbon::now()->toDateString() . '.csv';

        Storage::delete($filename);
        if (Storage::exists($filename . '.original')) {
            Storage::move($filename . '.original', $filename);
        }
        parent::tearDown();
    }

    /**
     * Test IPA index updates successfully.
     */
    public function testIPAIndexUpdated()
    {
        Event::fake();

        $job = new ProcessIPAList();
        $job->handle();

        $this->assertFileExists(storage_path('app/ipa_csv/ipa_csv_' . Carbon::now()->toDateString() . '.csv'));
        $this->assertFileIsReadable(storage_path('app/ipa_csv/ipa_csv_' . Carbon::now()->toDateString() . '.csv'));

        $handle = fopen(storage_path('app/ipa_csv/ipa_csv_' . Carbon::now()->toDateString() . '.csv'), 'rb');

        $this->assertNotNull($handle);
        $this->assertIsResource($handle);

        $this->assertGreaterThan(12, count(fgetcsv($handle, 0, "\t")));

        Event::assertDispatched(IPAUpdateCompleted::class);

        Event::assertNotDispatched(IPAUpdateFailed::class);
    }

    /**
     * Test IPA index update fails.
     */
    public function testIPAIndexFailed()
    {
        $filename = 'ipa_csv/ipa_csv_' . Carbon::now()->toDateString() . '.csv';
        $file = File::get(storage_path('testing/amministrazioni_bugged.csv'));
        if (Storage::exists($filename)) {
            Storage::delete($filename);
        }
        Storage::put($filename, $file);

        Event::fake();

        $job = new ProcessIPAList();
        $job->handle();

        Event::assertDispatched(IPAUpdateFailed::class);
        Event::assertNotDispatched(IPAUpdateCompleted::class);
    }

    /**
     * Test registered Public Administration check with no updates.
     */
    public function testNoPublicAdministrationUpdate(): void
    {
        Event::fake();

        $public_administration = factory(PublicAdministration::class)->create([
            'ipa_code' => 'camera',
            'name' => 'Camera dei Deputati',
            'pec_address' => 'camera_protcentrale@certcamera.it',
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

        $job = new ProcessIPAList();
        $job->handle();

        Event::assertNotDispatched(PublicAdministrationUpdated::class);
        Event::assertNotDispatched(PublicAdministrationWebsiteUpdated::class);
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
            'pec_address' => 'camera_protcentrale@certcamera.it',
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

        $job = new ProcessIPAList();
        $job->handle();

        Event::assertDispatched(PublicAdministrationWebsiteUpdated::class, function ($event) use ($public_administration, $website) {
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
