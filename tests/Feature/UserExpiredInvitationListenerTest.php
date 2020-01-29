<?php

namespace Tests\Feature;

use App\Enums\Logs\EventType;
use App\Enums\UserRole;
use App\Events\User\UserInvitationLinkExpired;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Notifications\UserExpiredInvitationLinkVisitedEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class UserExpiredInvitationListenerTest extends TestCase
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

    public function testUserUsedExpiredLink(): void
    {
        $user = factory(User::class)->state('invited')->create();
        $this->publicAdministration->users()->sync([$user->id], false);

        $this->expectLogMessage('info', [
            'User ' . $user->uuid . ' tried to use an expired invitation link.',
            [
                'user' => $user->uuid,
                'pa' => $this->publicAdministration->ipa_code,
                'event' => EventType::EXPIRED_USER_INVITATION_USED,
            ],
        ]);

        event(new UserInvitationLinkExpired($user));

        Notification::assertSentTo(
            [$this->publicAdministrationAdmin],
            UserExpiredInvitationLinkVisitedEmail::class,
            function ($notification, $channels) use ($user) {
                $this->assertEquals($channels, ['mail']);
                $mail = $notification->toMail($this->publicAdministrationAdmin)->build();
                $this->assertEquals($this->publicAdministrationAdmin->uuid, $mail->viewData['user']['uuid']);
                $this->assertEquals($user->uuid, $mail->viewData['invitedUser']['uuid']);
                $this->assertEquals($mail->subject, __('[Info] - Avviso di utilizzo di un invito scaduto'));

                return $mail->hasTo($this->publicAdministrationAdmin->email, $this->publicAdministrationAdmin->full_name);
            }
        );
    }

    public function testSuperAdminUsedExpiredLink(): void
    {
        $user = factory(User::class)->state('invited')->create();
        Bouncer::scope()->onceTo(0, function () use ($user) {
            $user->assign(UserRole::SUPER_ADMIN);
        });

        event(new UserInvitationLinkExpired($user));

        Notification::assertNothingSent();
    }
}
