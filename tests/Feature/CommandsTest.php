<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Jobs\ProcessPendingWebsites;
use App\Jobs\ProcessPublicAdministrationsUpdateFromIpa;
use App\Models\Website;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

/**
 * Custom application commands tests.
 */
class CommandsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test check pending website command.
     *
     * @throws \App\Exceptions\AnalyticsServiceException if unable to connect to the Analytics Service
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to Analytics Service
     * @throws \GuzzleHttp\Exception\GuzzleException if unable to inject tracking request
     * @throws \App\Exceptions\CommandErrorException if Analytics Service command finishes with error
     */
    public function testCheckPendingWebsites(): void
    {
        Bus::fake();

        $this->artisan('app:check-websites');

        Bus::assertDispatched(ProcessPendingWebsites::class);
    }

    /**
     * Test create roles command.
     */
    public function testCreateRoles(): void
    {
        $this->assertDatabaseMissing('roles', [
            'name' => UserRole::REGISTERED,
            'name' => UserRole::DELEGATED,
            'name' => UserRole::ADMIN,
            'name' => UserRole::SUPER_ADMIN,
        ]);
        $this->assertDatabaseMissing('abilities', [
            'name' => UserPermission::ACCESS_ADMIN_AREA,
            'name' => UserPermission::MANAGE_USERS,
            'name' => UserPermission::MANAGE_WEBSITES,
            'name' => UserPermission::MANAGE_ANALYTICS,
            'name' => UserPermission::READ_ANALYTICS,
            'name' => UserPermission::DO_NOTHING,
        ]);
        $this->artisan('app:init-permissions');
        $this->assertDatabaseHas('roles', [
            'name' => UserRole::REGISTERED,
            'name' => UserRole::DELEGATED,
            'name' => UserRole::ADMIN,
            'name' => UserRole::SUPER_ADMIN,
        ]);
        $this->assertDatabaseHas('abilities', [
            'name' => UserPermission::ACCESS_ADMIN_AREA,
            'name' => UserPermission::MANAGE_USERS,
            'name' => UserPermission::MANAGE_WEBSITES,
            'name' => UserPermission::MANAGE_ANALYTICS,
            'name' => UserPermission::READ_ANALYTICS,
            'name' => UserPermission::DO_NOTHING,
        ]);
    }

    /**
     * Test update IPA command.
     */
    public function testUpdatePublicAdministrationsFromIpa(): void
    {
        Bus::fake();

        $this->artisan('app:update-ipa');

        Bus::assertDispatched(ProcessPublicAdministrationsUpdateFromIpa::class);
    }
}
