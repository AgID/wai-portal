<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserStatus;
use App\Models\PublicAdministration;
use App\Models\User;
use App\Models\Website;
use Faker\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

/**
 * Analytics authorization middleware tests.
 */
class AuthorizePublicAdminisrtationMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The authenticated user.
     *
     * @var User the user
     */
    private $user;

    /**
     * The public administration the user belongs to.
     *
     * @var PublicAdministration the public administration
     */
    private $publicAdministration;

    /**
     * The public administration website.
     *
     * @var Website the website
     */
    private $website;

    /* Fake data generator.
     *
     * @var Generator the generator
     */
    private $faker;

    /**
     * Pre-test setup.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->faker = Factory::create();
        $this->publicAdministration = factory(PublicAdministration::class)->create();
        $this->publicAdministration->users()->sync([$this->user->id => [
            'user_status' => UserStatus::ACTIVE,
            'user_email' => $this->faker->unique()->safeEmail,
        ]]);

        Bouncer::scope()->onceTo($this->publicAdministration->id, function () {
            $this->user->allow(UserPermission::MANAGE_USERS);
            $this->user->allow(UserPermission::MANAGE_ANALYTICS, $this->website);
        });

        Bouncer::dontCache();
    }

    /**
     * Test user authorization on public administration granted.
     */
    public function testPermissionOnPublicAdministrationGranted(): void
    {
        /* $secondPublicAdministration = factory(PublicAdministration::class)->create();
        $secondPublicAdministration->users()->sync([$this->user->id => [
            'user_status' => UserStatus::ACTIVE,
            'user_email' => $this->faker->unique()->safeEmail,
        ]]); */

        error_log('# ' . $this->publicAdministration->id);
        error_log(print_r($this->user->publicAdministrations->toArray(), true));

        $this->actingAs($this->user)
            ->withSession(['tenant_id' => $this->publicAdministration->id])
            ->get('websites')
            ->assertStatus(200);
    }
}
