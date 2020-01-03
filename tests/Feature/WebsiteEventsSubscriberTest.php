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

class WebsiteEventsSubscriberTest extends TestCase
{
    use RefreshDatabase;

    private $publicAdministration;

    private $website;

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

        Config::set('analytics-service.viewer_login', 'public_viewer');
    }

    public function testWebsiteActivatedRollUpsUpdate(): void
    {
        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('setWebsiteAccess')
                    ->withArgs([
                        'public_viewer',
                        WebsiteAccessType::VIEW,
                        $this->website->analytics_id,
                    ])
                    ->once();
            });
        });

        $this->partialMock(HasAnalyticsDashboard::class)
            ->shouldReceive('updateRollUp')
            ->withArgs([$this->website]);

        event(new WebsiteActivated($this->website));
    }

    public function testWebsiteActivatedRollUpsUpdateFail(): void
    {
        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('setWebsiteAccess')
                    ->withArgs([
                        'public_viewer',
                        WebsiteAccessType::VIEW,
                        $this->website->analytics_id,
                    ])
                    ->once()
                    ->andThrow(\Exception::class, 'Public rollup exception testing');
            });
        });

        $this->partialMock(HasAnalyticsDashboard::class)
            ->shouldReceive('updateRollUp')
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
                        'public_viewer',
                        WebsiteAccessType::VIEW,
                        $this->website->analytics_id,
                    ])
                    ->once();
            });
        });

        $this->partialMock(HasAnalyticsDashboard::class)
            ->shouldNotReceive('updateRollUp');

        event(new WebsiteActivated($this->website));
    }
}
