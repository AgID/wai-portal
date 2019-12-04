<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Italia\SPIDAuth\SPIDAuth;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class EnforceRuleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();

        Bouncer::dontCache();

        $this->withoutExceptionHandling();

        $this->app->bind('SPIDAuth', function () {
            return $this->partialMock(SPIDAuth::class, function ($mock) {
                $mock->shouldReceive('isAuthenticated')
                    ->once()
                    ->andReturn(false);
            });
        });

        Route::middleware('enforce.rules:forbid-spid,forbid-invited')->get('_test/rules-enforced', function () {
            return Response::HTTP_OK;
        })->name('rule-enforcer-test');
    }

    public function testSPIDAuthenticatedAuthorizationFail(): void
    {
        $this->app->bind('SPIDAuth', function () {
            return $this->partialMock(SPIDAuth::class, function ($mock) {
                $mock->shouldReceive('isAuthenticated')
                    ->once()
                    ->andReturn(true);
            });
        });

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('SPID authenticated users are not authorized for route rule-enforcer-test.');

        $this->get('_test/rules-enforced');
    }

    public function testAuthorizationGranted(): void
    {
        $this->actingAs($this->user)
            ->get('_test/rules-enforced')
            ->assertOk();
    }

    public function testInvitedUserAuthorizationFail(): void
    {
        $this->user->status = UserStatus::INVITED;
        $this->actingAs($this->user)
            ->get('_test/rules-enforced')
            ->assertRedirect(route('verification.notice'));

        $this->user->allow(UserPermission::ACCESS_ADMIN_AREA);

        $this->actingAs($this->user)
            ->get('_test/rules-enforced')
            ->assertRedirect(route('admin.verification.notice'));
    }
}
