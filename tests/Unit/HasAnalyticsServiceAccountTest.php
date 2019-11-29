<?php

namespace Tests\Unit;

use App\Exceptions\AnalyticsServiceAccountException;
use App\Models\User;
use App\Services\MatomoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class HasAnalyticsServiceAccountTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    public function testHasAnalyticsPassword(): void
    {
        $this->assertFalse($this->user->hasAnalyticsServiceAccount());

        $this->user->partial_analytics_password = Str::random(rand(32, 48));
        $this->user->save();

        $this->assertTrue($this->user->hasAnalyticsServiceAccount());
    }

    public function testGetAnalyticsServiceAccountTokenAuthException(): void
    {
        $this->expectException(AnalyticsServiceAccountException::class);
        $this->user->getAnalyticsServiceAccountTokenAuth();
    }

    public function testGetAnalyticsServiceAccountTokenAuth(): void
    {
        $this->user->partial_analytics_password = Str::random(rand(32, 48));
        $this->user->save();

        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('getUserAuthToken')
                    ->withArgs([
                        $this->user->uuid,
                        md5($this->user->analytics_password),
                    ])
                    ->once()
                    ->andReturn('faketoken');
            });
        });

        $this->assertEquals('faketoken', $this->user->getAnalyticsServiceAccountTokenAuth());
    }

    public function testRegisterAnalyticsServiceAccount(): void
    {
        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('registerUser')
                    ->withArgs([
                        $this->user->uuid,
                        $this->user->analytics_password,
                        $this->user->email,
                    ])
                    ->once();
            });
        });

        $this->user->registerAnalyticsServiceAccount();

        $this->assertNotEmpty($this->user->partial_analytics_password);
    }

    public function testUpdateAnalyticsServiceAccountEmail(): void
    {
        $this->user->partial_analytics_password = Str::random(rand(32, 48));
        $this->user->save();

        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('getUserAuthToken')
                    ->withArgs([
                        $this->user->uuid,
                        md5($this->user->analytics_password),
                    ])
                    ->andReturn('faketoken');
                $mock->shouldReceive('updateUserEmail')
                    ->withArgs([
                        $this->user->uuid,
                        $this->user->email,
                        $this->user->analytics_password,
                        'faketoken',
                    ]);
            });
        });

        $this->user->updateAnalyticsServiceAccountEmail();
    }

    public function testDeleteAnalyticsServiceAccount(): void
    {
        $this->user->partial_analytics_password = Str::random(rand(32, 48));
        $this->user->save();

        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('deleteUser')
                    ->withArgs([
                        $this->user->uuid,
                    ])
                    ->once();
            });
        });

        $this->user->deleteAnalyticsServiceAccount();

        $this->assertNull($this->user->partial_analytics_password);
    }
}
