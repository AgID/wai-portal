<?php

namespace Tests\Feature;

use App\Enums\UserStatus;
use App\Events\User\UserInvitationLinkExpired;
use App\Exceptions\ExpiredInvitationException;
use App\Exceptions\ExpiredVerificationException;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ValidateSignatureMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();

        Route::middleware(['signed', 'web'])->get('_test/signature', function () {
            return Response::HTTP_OK;
        });

        Route::middleware(['signed', 'web'])->get('_test/verify/{uuid}', function (string $uuid) {
            return Response::HTTP_OK;
        })->name('verification.verify');

        $this->withoutExceptionHandling();
    }

    public function testSignatureValidated(): void
    {
        URL::shouldReceive('hasValidSignature')
            ->once()
            ->andReturnTrue();
        URL::makePartial();

        $this->get('_test/signature')
            ->assertOk();
    }

    public function testInvalidSignature(): void
    {
        URL::shouldReceive('hasValidSignature')
            ->once()
            ->andReturnFalse();
        URL::makePartial();

        $this->expectException(InvalidSignatureException::class);

        $this->get('_test/signature');
    }

    public function testExpiredVerification(): void
    {
        Event::fake();

        URL::shouldReceive('hasValidSignature')
            ->once()
            ->andReturnFalse();
        URL::shouldReceive('hasCorrectSignature')
            ->once()
            ->andReturnTrue();
        URL::makePartial();

        $this->expectException(ExpiredVerificationException::class);

        $this->get('_test/verify/' . $this->user->uuid);

        Event::assertNotDispatched(UserInvitationLinkExpired::class);
    }

    public function testExpiredInvitation(): void
    {
        $this->user->status = UserStatus::INVITED;
        $this->user->save();

        Event::fake();

        URL::shouldReceive('hasValidSignature')
            ->once()
            ->andReturnFalse();
        URL::shouldReceive('hasCorrectSignature')
            ->once()
            ->andReturnTrue();
        URL::makePartial();

        $this->expectException(ExpiredInvitationException::class);

        $this->get('_test/verify/' . $this->user->uuid);

        Event::assertDispatched(UserInvitationLinkExpired::class, function ($event) {
            return $event->getUser()->uuid === $this->user->uuid;
        });
    }
}
