<?php

namespace Tests\Feature;

use App\Enums\Logs\EventType;
use App\Enums\WebsiteType;
use App\Events\PublicAdministration\PublicAdministrationActivated;
use App\Models\PublicAdministration;
use App\Models\Website;
use App\Services\MatomoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * Public administrations events listener tests.
 */
class PublicAdministrationEventsSubscriberTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The public administration.
     *
     * @var PublicAdministration the public administration
     */
    public $publicAdministration;

    /**
     * Pre-tests setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->publicAdministration = factory(PublicAdministration::class)->create();
        factory(Website::class)->create([
            'type' => WebsiteType::PRIMARY,
            'analytics_id' => 1,
            'public_administration_id' => $this->publicAdministration->id,
        ]);
    }

    /**
     * Test roll-up registering successful on public administration activation.
     */
    public function testPublicAdministrationActivatedRollUpRegistering(): void
    {
        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('registerRollUp')
                    ->once()
                    ->andReturn(1);

                $mock->shouldReceive('registerUser')
                    ->once();

                $mock->shouldReceive('getUserAuthToken')
                    ->once()
                    ->andReturn('faketoken');

                $mock->shouldReceive('setWebsiteAccess')
                    ->once();

                $mock->shouldReceive('setWebsiteAccess')
                    ->once();
            });
        });

        Log::shouldReceive('notice')
            ->withArgs([
                'Public Administration ' . $this->publicAdministration->info . ' activated',
                [
                    'event' => EventType::PUBLIC_ADMINISTRATION_ACTIVATED,
                    'pa' => $this->publicAdministration->ipa_code,
                ],
            ]);

        event(new PublicAdministrationActivated($this->publicAdministration));
    }

    /**
     * Test roll-up registering throwing exception.
     */
    public function testPublicAdministrationActivatedRollUpRegisteringFail(): void
    {
        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('registerRollUp')
                    ->andThrow(\Exception::class, 'Public administration roll-up exception testing');
            });
        });

        Log::shouldReceive('error')
            ->withSomeOfArgs('Public administration roll-up exception testing');

        Log::shouldReceive('notice')
            ->withArgs([
                'Public Administration ' . $this->publicAdministration->info . ' activated',
                [
                    'event' => EventType::PUBLIC_ADMINISTRATION_ACTIVATED,
                    'pa' => $this->publicAdministration->ipa_code,
                ],
            ]);

        event(new PublicAdministrationActivated($this->publicAdministration));
    }
}
