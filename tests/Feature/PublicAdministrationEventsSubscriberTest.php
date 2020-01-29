<?php

namespace Tests\Feature;

use App\Enums\Logs\EventType;
use App\Enums\WebsiteType;
use App\Events\PublicAdministration\PublicAdministrationActivated;
use App\Events\PublicAdministration\PublicAdministrationActivationFailed;
use App\Events\PublicAdministration\PublicAdministrationNotFoundInIpa;
use App\Events\PublicAdministration\PublicAdministrationPrimaryWebsiteUpdated;
use App\Events\PublicAdministration\PublicAdministrationPurged;
use App\Events\PublicAdministration\PublicAdministrationRegistered;
use App\Events\PublicAdministration\PublicAdministrationUpdated;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use App\Notifications\PublicAdministrationActivatedEmail;
use App\Notifications\PublicAdministrationPurgedEmail;
use App\Notifications\PublicAdministrationRegisteredEmail;
use App\Notifications\RTDPublicAdministrationRegisteredEmail;
use App\Services\MatomoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
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

    public $website;

    public $user;

    /**
     * Pre-tests setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->publicAdministration = factory(PublicAdministration::class)->create();
        $this->user = factory(User::class)->create();
        $this->publicAdministration->users()->sync([$this->user->id]);
        $this->publicAdministration->save();
        $this->website = factory(Website::class)->create([
            'type' => WebsiteType::PRIMARY,
            'analytics_id' => 1,
            'public_administration_id' => $this->publicAdministration->id,
        ]);

        Notification::fake();
    }

    /**
     * Test roll-up registering successful on public administration activation.
     */
    public function testPublicAdministrationActivate(): void
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

        $this->expectLogMessage('notice', [
            'Public Administration ' . $this->publicAdministration->info . ' activated',
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_ACTIVATED,
                'pa' => $this->publicAdministration->ipa_code,
            ],
        ]);

        event(new PublicAdministrationActivated($this->publicAdministration));

        Notification::assertSentTo(
            [$this->user],
            PublicAdministrationActivatedEmail::class,
            function ($notification, $channels) {
                $this->assertEquals($channels, ['mail']);
                $mail = $notification->toMail($this->user)->build();
                $this->assertEquals($this->user->uuid, $mail->viewData['user']['uuid']);
                $this->assertEquals($this->publicAdministration->ipa_code, $mail->viewData['publicAdministration']['ipa_code']);
                $this->assertEquals($mail->subject, __('[Info] - Pubblica amministrazione attiva'));

                return $mail->hasTo($this->user->email, $this->user->full_name);
            }
        );
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

        $this->expectLogMessage('notice', [
            'Public Administration ' . $this->publicAdministration->info . ' activated',
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_ACTIVATED,
                'pa' => $this->publicAdministration->ipa_code,
            ],
        ]);

        event(new PublicAdministrationActivated($this->publicAdministration));

        Notification::assertSentTo(
            [$this->user],
            PublicAdministrationActivatedEmail::class
        );
    }

    public function testPublicAdministrationRegisteredWithRTD(): void
    {
        Cache::shouldReceive('forget')->once()->withArgs([
            PublicAdministration::PUBLIC_ADMINISTRATION_COUNT_KEY,
        ]);

        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('getJavascriptSnippet')
                    ->withArgs([
                        $this->website->analytics_id,
                    ])
                    ->once()
                    ->andReturn('fakesnippet');
            });
        });

        $this->expectLogMessage('notice', [
            'User ' . $this->user->uuid . ' registered Public Administration ' . $this->publicAdministration->info,
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_REGISTERED,
                'pa' => $this->publicAdministration->ipa_code,
                'user' => $this->user->uuid,
            ],
        ]);

        event(new PublicAdministrationRegistered($this->publicAdministration, $this->user));

        Notification::assertSentTo(
            [$this->user],
            PublicAdministrationRegisteredEmail::class,
            function ($notification, $channels) {
                $this->assertEquals($channels, ['mail']);
                $mail = $notification->toMail($this->user)->build();
                $this->assertEquals($this->user->uuid, $mail->viewData['user']['uuid']);
                $this->assertEquals($this->publicAdministration->ipa_code, $mail->viewData['publicAdministration']['ipa_code']);
                $this->assertEquals('fakesnippet', $mail->viewData['javascriptSnippet']);
                $this->assertEquals($mail->subject, __('Pubblica amministrazione registrata'));

                return $mail->hasTo($this->user->email, $this->user->full_name);
            }
        );

        Notification::assertSentTo(
            [$this->publicAdministration],
            RTDPublicAdministrationRegisteredEmail::class,
            function ($notification, $channels) {
                $this->assertEquals($channels, ['mail']);
                $mail = $notification->toMail($this->publicAdministration)->build();
                $this->assertEquals($this->publicAdministration->ipa_code, $mail->viewData['publicAdministration']['ipa_code']);
                $this->assertEquals($mail->subject, __('[Info] - Pubblica amministrazione registrata'));

                return $mail->hasTo($this->publicAdministration->rtd_mail, $this->publicAdministration->rtd_name);
            }
        );
    }

    public function testPublicAdministrationRegisteredWithoutRTD(): void
    {
        Event::fakeFor(function () {
            $this->publicAdministration->rtd_mail = null;
            $this->publicAdministration->rtd_name = null;
            $this->publicAdministration->save();
        });

        Cache::shouldReceive('forget')->once()->withArgs([
            PublicAdministration::PUBLIC_ADMINISTRATION_COUNT_KEY,
        ]);

        $this->app->bind('analytics-service', function () {
            return $this->partialMock(MatomoService::class, function ($mock) {
                $mock->shouldReceive('getJavascriptSnippet')
                    ->withArgs([
                        $this->website->analytics_id,
                    ])
                    ->once()
                    ->andReturn('fakesnippet');
            });
        });

        $this->expectLogMessage('notice', [
            'User ' . $this->user->uuid . ' registered Public Administration ' . $this->publicAdministration->info,
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_REGISTERED,
                'pa' => $this->publicAdministration->ipa_code,
                'user' => $this->user->uuid,
            ],
        ]);

        event(new PublicAdministrationRegistered($this->publicAdministration, $this->user));

        Notification::assertSentTo(
            [$this->user],
            PublicAdministrationRegisteredEmail::class,
            function ($notification, $channels) {
                $this->assertEquals($channels, ['mail']);
                $mail = $notification->toMail($this->user)->build();
                $this->assertEquals($this->user->uuid, $mail->viewData['user']['uuid']);
                $this->assertEquals($this->publicAdministration->ipa_code, $mail->viewData['publicAdministration']['ipa_code']);
                $this->assertEquals('fakesnippet', $mail->viewData['javascriptSnippet']);
                $this->assertEquals($mail->subject, __('Pubblica amministrazione registrata'));

                return $mail->hasTo($this->user->email, $this->user->full_name);
            }
        );

        Notification::assertNotSentTo(
            [$this->publicAdministration],
            RTDPublicAdministrationRegisteredEmail::class
        );
    }

    public function testPublicAdministrationPurged(): void
    {
        Cache::shouldReceive('forget')->once()->withArgs([PublicAdministration::PUBLIC_ADMINISTRATION_COUNT_KEY]);

        $this->expectLogMessage('notice', [
            'Public Administration "' . $this->publicAdministration->name . '" [' . $this->publicAdministration->ipa_code . '] purged',
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_PURGED,
                'pa' => $this->publicAdministration->ipa_code,
            ],
        ]);

        event(new PublicAdministrationPurged($this->publicAdministration->toJson(), $this->user));

        Notification::assertSentTo(
            [$this->user],
            PublicAdministrationPurgedEmail::class,
            function ($notification, $channels) {
                $this->assertEquals($channels, ['mail']);
                $mail = $notification->toMail($this->user)->build();
                $this->assertEquals($this->user->uuid, $mail->viewData['user']['uuid']);
                $this->assertEquals($this->publicAdministration->ipa_code, $mail->viewData['publicAdministration']->ipa_code);
                $this->assertEquals($mail->subject, __('Pubblica amministrazione eliminata'));

                return $mail->hasTo($this->user->email, $this->user->full_name);
            }
        );
    }

    public function testPublicAdministrationPrimaryWebsiteUpdated(): void
    {
        $this->expectLogMessage('warning', [
            'Public Administration ' . $this->publicAdministration->info . ' primary website was changed in IPA index [' . e('https://newurl.local') . '].',
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_PRIMARY_WEBSITE_CHANGED,
                'pa' => $this->publicAdministration->ipa_code,
            ],
        ]);

        event(new PublicAdministrationPrimaryWebsiteUpdated($this->publicAdministration, $this->website, 'https://newurl.local'));
    }

    public function testPublicAdministrationUpdated(): void
    {
        $this->expectLogMessage('notice', [
            'Public Administration ' . $this->publicAdministration->info . ' updated',
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_UPDATED,
                'pa' => $this->publicAdministration->ipa_code,
            ],
        ]);

        event(new PublicAdministrationUpdated($this->publicAdministration, []));
    }

    public function testPublicAdministrationNotFoundInIpa(): void
    {
        $this->expectLogMessage('warning', [
            'Public Administration ' . $this->publicAdministration->info . ' not found',
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_UPDATED,
                'pa' => $this->publicAdministration->ipa_code,
            ],
        ]);

        event(new PublicAdministrationNotFoundInIpa($this->publicAdministration));
    }

    public function testPublicAdministrationActivationFailed(): void
    {
        $this->expectLogMessage('error', [
            'Public Administration ' . $this->publicAdministration->info . ' activation failed: Fake activation error message',
            [
                'event' => EventType::PUBLIC_ADMINISTRATION_ACTIVATION_FAILED,
                'pa' => $this->publicAdministration->ipa_code,
            ],
        ]);

        event(new PublicAdministrationActivationFailed($this->publicAdministration, 'Fake activation error message'));
    }
}
