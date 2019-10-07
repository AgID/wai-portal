<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Events\User\UserInvitationLinkExpired;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Notifications\ExpiredInvitationLinkVisitedEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class ValidateSignatureMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private $publicAdministration;

    private $publicAdministrationAdmin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->publicAdministration = factory(PublicAdministration::class)->state('active')->create();
        $this->publicAdministrationAdmin = factory(User::class)->state('active')->create();
        $this->publicAdministration->users()->sync([$this->publicAdministrationAdmin->id]);
        Bouncer::dontCache();
        Bouncer::scope()->onceTo($this->publicAdministration->id, function () {
            $this->publicAdministrationAdmin->assign(UserRole::ADMIN);
        });
        Notification::fake();
    }

    public function testExpiredLinkVisited(): void
    {
        $user = factory(User::class)->state('invited')->create();
        $this->publicAdministration->users()->sync([$user->id]);
        event(new UserInvitationLinkExpired($user));

        Notification::assertSentTo(
            [$this->publicAdministrationAdmin],
            ExpiredInvitationLinkVisitedEmail::class,
            function ($notification, $channels) use ($user) {
                $this->assertEquals($channels, ['mail']);
                $mail = $notification->toMail($this->publicAdministrationAdmin)->build();
                $this->assertEquals(
                    $mail->viewData, [
                    'locale' => Lang::getLocale(),
                    'fullName' => $this->publicAdministrationAdmin->full_name,
                    'invitedFullName' => $user->full_name,
                    'profileUrl' => route('users.show', ['user' => $user]),
                ]);
                $this->assertEquals($mail->subject, __('[Info] - Avviso utilizzo invito scaduto'));

                return $mail->hasTo($this->publicAdministrationAdmin->email, $this->publicAdministrationAdmin->full_name);
            }
        );
    }

    public function testExpiredLinkVisitedNotInvited(): void
    {
        $user = factory(User::class)->create();
        $this->publicAdministration->users()->sync([$user->id]);
        event(new UserInvitationLinkExpired($user));
        Notification::assertNothingSent();
    }

    public function testExpiredLinkVisitedSuperAdmin(): void
    {
        $user = factory(User::class)->state('invited')->create();
        $this->publicAdministration->users()->sync([$user->id]);
        Bouncer::scope()->onceTo(0, function () use ($user) {
            $user->assign(UserRole::SUPER_ADMIN);
        });
        event(new UserInvitationLinkExpired($user));
        Notification::assertNothingSent();
    }
}
