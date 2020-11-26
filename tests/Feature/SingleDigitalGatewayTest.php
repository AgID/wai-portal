<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Date;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/**
 * Super admin password management test.
 */
class SingleDigitalGatewayTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Super admin user.
     *
     * @var User the user
     */
    private $user;

    /**
     * Pre-test setup.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create([
            'status' => UserStatus::ACTIVE,
            'email_verified_at' => Date::now(),
        ]);

        Bouncer::dontCache();
        Bouncer::scope()->onceTo(0, function () {
            $this->user->assign(UserRole::SUPER_ADMIN);
            $this->user->allow(UserPermission::MANAGE_USERS);
            $this->user->allow(UserPermission::ACCESS_ADMIN_AREA);
        });
    }

    /*
     * Test payload generation.
     */
    public function testPayloadGeneration(): void
    {
        $this->actingAs($this->user)
            ->json('GET', route('admin.sdg.dataset.show'))
            ->assertOk();
    }
}
