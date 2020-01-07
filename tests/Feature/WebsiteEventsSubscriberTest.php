<?php

namespace Tests\Feature;

use App\Enums\Logs\EventType;
use App\Enums\WebsiteAccessType;
use App\Enums\WebsiteType;
use App\Events\Website\WebsiteActivated;
use App\Models\PublicAdministration;
use App\Models\Website;
use App\Services\MatomoService;
use App\Traits\HasAnalyticsDashboard;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

/**
 * Websites events listener tests.
 */
class WebsiteEventsSubscriberTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The public administration the website belongs to.
     *
     * @var PublicAdministration the public administration
     */
    private $publicAdministration;

    /**
     * The activated website.
     *
     * @var Website the website
     */
    private $website;

    /**
     * Pre-tests setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->publicAdministration = factory(PublicAdministration::class)->create([
            'rollup_id' => 1,
            'token_auth' => 'faketoken',
        ]);
        $this->website = factory(Website::class)->create([
            'public_administration_id' => $this->publicAdministration->id,
            'analytics_id' => 1,
        ]);
    }

    /**
     * Test roll-up sites updated.
     */
    public function testWebsiteActivatedRollUpsUpdate(): void
    {
        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('setWebsiteAccess')
                    ->withArgs([
                        'anonymous',
                        WebsiteAccessType::VIEW,
                        $this->website->analytics_id,
                    ])
                    ->once();
            });
        });

        $this->partialMock(HasAnalyticsDashboard::class)
            ->shouldReceive('addToRollUp')
            ->withArgs([$this->website]);

        event(new WebsiteActivated($this->website));
    }

    /**
     * Test roll-up sites throwing exception.
     */
    public function testWebsiteActivatedRollUpsUpdateFail(): void
    {
        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('setWebsiteAccess')
                    ->withArgs([
                        'anonymous',
                        WebsiteAccessType::VIEW,
                        $this->website->analytics_id,
                    ])
                    ->once()
                    ->andThrow(\Exception::class, 'Public rollup exception testing');
            });
        });

        $this->partialMock(HasAnalyticsDashboard::class)
            ->shouldReceive('addToRollUp')
            ->withArgs([$this->website])
            ->andThrow(\Exception::class, 'Public administration rollup exception reporting');

        Log::shouldReceive('notice')
            ->withArgs([
                'Website ' . $this->website->info . ' activated',
                [
                    'event' => EventType::WEBSITE_ACTIVATED,
                    'website' => $this->website->id,
                    'pa' => $this->website->publicAdministration->ipa_code,
                ],
            ]);

        Log::shouldReceive('error')
            ->withSomeOfArgs('Public rollup exception testing');

        Log::shouldReceive('error')
            ->withSomeOfArgs('Public administration rollup exception reporting');

        event(new WebsiteActivated($this->website));
    }

    /**
     * Test roll-up sites update for primary website.
     */
    public function testPrimaryWebsiteActivatedRollUpsUpdate(): void
    {
        Event::fakeFor(function () {
            $this->website->type = WebsiteType::PRIMARY;
            $this->website->save();
        });

        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('setWebsiteAccess')
                    ->withArgs([
                        'anonymous',
                        WebsiteAccessType::VIEW,
                        $this->website->analytics_id,
                    ])
                    ->once();
            });
        });

        $this->partialMock(HasAnalyticsDashboard::class)
            ->shouldNotReceive('addToRollUp');

        event(new WebsiteActivated($this->website));
    }
}
