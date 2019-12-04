<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AuthenticateAdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();

        Route::middleware('auth.admin')->get('_test/admin-authentication', function () {
            return Response::HTTP_OK;
        });
    }

    public function testMissingSuperAdminAuthentication(): void
    {
        $this->get('_test/admin-authentication')
            ->assertRedirect(route('admin.login.show'));
    }

    public function testSuperAdminAuthenticationSuccessful(): void
    {
        $this->user->assign(UserRole::SUPER_ADMIN);

        $this->actingAs($this->user)
            ->get('_test/admin-authentication')
            ->assertOk();
    }

    public function testNotSuperAdminAuthentication(): void
    {
        $this->withoutExceptionHandling();

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('Current user in not a super administrator.');

        $this->actingAs($this->user)
            ->get('_test/admin-authentication');
    }
}
