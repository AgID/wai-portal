<?php

namespace Tests\Feature;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CronAuthenticationMiddlewareTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Config::set('cron-auth.cron_token', 'fake-cron-token');

        Route::middleware('cron.auth')->get('_test/cron-route', function () {
            return Response::HTTP_OK;
        });
    }

    public function testMissingTokenAuthorizationFail(): void
    {
        $this->get('_test/cron-route')
            ->assertForbidden()
            ->assertJson(['error' => 'Unauthorized']);
    }

    public function testWrongTokenAuthorizationFail(): void
    {
        $this->get('_test/cron-route?token=wrong-token')
            ->assertForbidden()
            ->assertJson(['error' => 'Unauthorized']);
    }

    public function testAuthorizationGranted(): void
    {
        $this->get('_test/cron-route?token=fake-cron-token')
            ->assertOk();
    }
}
