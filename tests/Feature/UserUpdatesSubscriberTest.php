<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Events\User\UserEmailChanged;
use App\Events\User\UserStatusChanged;
use App\Models\User;
use App\Services\MatomoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UserUpdatesSubscriberTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake([UserEmailChanged::class, UserStatusChanged::class]);

        $this->user = factory(User::class)->create();
    }

    public function testUpdatingWithEmailVerificationReset(): void
    {
        Event::fakeFor(function () {
            $this->user->email_verified_at = Date::now();
            $this->user->save();
        });

        $this->user->email = 'newfake@email.local';
        $this->user->save();

        $this->assertNull($this->user->email_verified_at);
    }

    public function testUpdatingWithoutEmailVerificationReset(): void
    {
        Event::fakeFor(function () {
            $this->user->email_verified_at = Date::now();
            $this->user->save();
        });

        $this->user->name = 'Fakename';
        $this->user->save();

        $this->assertNotNull($this->user->email_verified_at);
        $this->assertEquals('Fakename', $this->user->name);
    }

    public function testUpdatedEmailVerifiedWithAnalyticsAccount(): void
    {
        Event::fakeFor(function () {
            $this->user->partial_analytics_password = 'fakepassword';
            $this->user->save();
        });

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

        $this->user->email_verified_at = Date::now();
        $this->user->save();

        Event::assertNotDispatched(UserEmailChanged::class);

        Event::assertNotDispatched(UserStatusChanged::class);
    }

    public function testUpdatedEmailVerifiedWithoutAnalyticsAccount(): void
    {
        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class)
                ->shouldNotReceive('updateUserEmail');
        });

        $this->user->email_verified_at = Date::now();
        $this->user->save();

        Event::assertNotDispatched(UserEmailChanged::class);

        Event::assertNotDispatched(UserStatusChanged::class);
    }

    public function testUpdatedEmailChangedWithAnalyticsAccount(): void
    {
        Event::fakeFor(function () {
            $this->user->partial_analytics_password = 'fakepassword';
            $this->user->save();
        });

        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('getUserAuthToken')
                    ->withArgs([
                        $this->user->uuid,
                        md5($this->user->analytics_password),
                    ])
                    ->once()
                    ->andReturn('faketoken');
                $mock->shouldReceive('updateUserEmail')
                    ->withArgs([
                        $this->user->uuid,
                        'newfake@email.local',
                        $this->user->analytics_password,
                        'faketoken',
                    ])
                    ->once();
            });
        });

        $this->user->email = 'newfake@email.local';
        $this->user->save();

        Event::assertDispatched(UserEmailChanged::class, function ($event) {
            return 'newfake@email.local' === $event->getUser()->email;
        });

        Event::assertNotDispatched(UserStatusChanged::class);
    }

    public function testUpdatedEmailChangedWithoutAnalyticsAccount(): void
    {
        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class)
                ->shouldNotReceive('updateUserEmail');
        });

        $this->user->email = 'newfake@email.local';
        $this->user->save();

        Event::assertDispatched(UserEmailChanged::class, function ($event) {
            return 'newfake@email.local' === $event->getUser()->email;
        });

        Event::assertNotDispatched(UserStatusChanged::class);
    }

    public function testUpdatedStatusChanged(): void
    {
        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class)
                ->shouldNotReceive('updateUserEmail');
        });

        $oldStatus = $this->user->status;

        $this->user->status = UserStatus::ACTIVE;
        $this->user->save();

        Event::assertNotDispatched(UserEmailChanged::class);

        Event::assertDispatched(UserStatusChanged::class, function ($event) use ($oldStatus) {
            $eventUser = $event->getUser();

            return $this->user->uuid === $eventUser->uuid
                && $eventUser->status->is(UserStatus::ACTIVE)
                && $event->getOldStatus()->is($oldStatus);
        });
    }
}
