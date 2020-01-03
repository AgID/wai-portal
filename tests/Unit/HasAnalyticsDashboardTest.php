<?php

namespace Tests\Unit;

use App\Enums\WebsiteAccessType;
use App\Models\PublicAdministration;
use App\Models\Website;
use App\Services\MatomoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class HasAnalyticsDashboardTest extends TestCase
{
    use RefreshDatabase;

    private $publicAdministration;

    private $website;

    public function setUp(): void
    {
        parent::setUp();

        Event::fake();

        $this->publicAdministration = factory(PublicAdministration::class)->create();
        $this->website = factory(Website::class)->create([
            'public_administration_id' => $this->publicAdministration->id,
            'analytics_id' => 1,
        ]);
    }

    public function testHasRollUpFail(): void
    {
        $this->assertFalse($this->publicAdministration->hasRollUp());

        $this->publicAdministration->rollup_id = 1;
        $this->publicAdministration->save();

        $this->assertTrue($this->publicAdministration->hasRollUp());
    }

    public function testRegisterRollUp(): void
    {
        $rollUpId = 3;

        $this->app->bind('analytics-service', function () use ($rollUpId) {
            return $this->partialMock(MatomoService::class, function ($mock) use ($rollUpId) {
                $mock->shouldReceive('registerRollUp')
                    ->withArgs([
                        $this->publicAdministration->name,
                        [$this->website->analytics_id],
                    ])
                    ->once()
                    ->andReturn($rollUpId);

                $mock->shouldReceive('registerUser')
                    ->withArgs(function ($login, $password, $email) {
                        return $this->publicAdministration->ipa_code === $login
                            && (Str::slug($this->publicAdministration->ipa_code) . '@' . 'webanalyticsitalia.local') === $email;
                    })
                    ->once();

                $mock->shouldReceive('getUserAuthToken')
                    ->withArgs(function ($login, $password) {
                        return $this->publicAdministration->ipa_code === $login;
                    })
                    ->once()
                    ->andReturn('faketoken');

                $mock->shouldReceive('setWebsiteAccess')
                    ->withArgs([
                        $this->publicAdministration->ipa_code,
                        WebsiteAccessType::VIEW,
                        $rollUpId,
                    ])
                    ->once();

                $mock->shouldReceive('setWebsiteAccess')
                    ->withArgs([
                        $this->publicAdministration->ipa_code,
                        WebsiteAccessType::VIEW,
                        $this->website->analytics_id,
                    ])
                    ->once();
            });
        });

        $this->publicAdministration->registerRollUp();

        $this->assertEquals('faketoken', $this->publicAdministration->token_auth);
        $this->assertEquals($rollUpId, $this->publicAdministration->rollup_id);
    }

    public function testUpdateRollUp(): void
    {
        $this->publicAdministration->rollup_id = 3;
        $newWebsite = factory(Website::class)->create([
            'public_administration_id' => $this->publicAdministration->id,
            'analytics_id' => 2,
        ]);

        $this->app->bind('analytics-service', function () use ($newWebsite) {
            return $this->partialMock(MatomoService::class, function ($mock) use ($newWebsite) {
                $mock->shouldReceive('updateRollUp')
                    ->withArgs([
                        $this->publicAdministration->rollup_id,
                        [1, 2],
                    ])
                    ->once();

                $mock->shouldReceive('setWebsiteAccess')
                    ->withArgs([
                        $this->publicAdministration->ipa_code,
                        WebsiteAccessType::VIEW,
                        $newWebsite->analytics_id,
                    ])
                    ->once();
            });
        });

        $this->publicAdministration->updateRollUp($newWebsite);
    }

    public function testUpdateRollUpFail(): void
    {
        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldNotReceive('updateRollUp');
                $mock->shouldNotReceive('setWebsiteAccess');
            });
        });

        $this->publicAdministration->updateRollUp($this->website);
        $this->assertTrue(null === $this->publicAdministration->rollup_id && null === $this->publicAdministration->token_auth);
    }
}
