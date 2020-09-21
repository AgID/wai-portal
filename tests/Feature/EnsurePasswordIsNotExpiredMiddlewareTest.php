<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EnsurePasswordIsNotExpiredMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['password.not.expired', 'web'])->get('_test/password-expiration', function () {
            return Response::HTTP_OK;
        });

        $this->user = factory(User::class)->create();
    }

    public function testExpiredPasswordAuthorizationFail(): void
    {
        $this->user->password_changed_at = Date::now()->subDays(config('auth.password_expiry'));
        $this->user->save();

        $this->actingAs($this->user)
            ->get('_test/password-expiration')
            ->assertRedirect(route('admin.password.change.show'))
            ->assertSessionHas('notification', [
                'title' => __('scadenza password'),
                'message' => __('La password è scaduta e deve essere cambiata.'),
                'status' => 'warning',
                'icon' => 'it-error',
            ]);
    }

    public function testAuthorizationGranted(): void
    {
        $this->actingAs($this->user)
            ->get('_test/password-expiration')
            ->assertOk();
    }
}
