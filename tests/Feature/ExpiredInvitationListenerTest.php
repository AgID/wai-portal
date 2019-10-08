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

/**
 * Dynamically generated URL validation middleware test.
 */
class ExpiredInvitationListenerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The testing public administration.
     *
     * @var PublicAdministration the public administration
     */
    private $publicAdministration;

    /**
     * The public administration administrator.
     *
     * @var User the public administration administrator
     */
    private $publicAdministrationAdmin;

    /**
     * Pre-test setup.
     */
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

    /**
     * Test notification to public administrator is sent for expired verification link event.
     */
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
}
