<?php

namespace Tests\Feature;

use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HelpersTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    protected function tearDown(): void
    {
        session()->invalidate();
        auth()->logout();
        parent::tearDown();
    }

    public function testCurrentPublicAdministrationExists(): void
    {
        $publicAdministration = factory(PublicAdministration::class)->create();
        $publicAdministration->users()->sync($this->user->id);

        auth()->login($this->user);
        session()->put('tenant_id', $publicAdministration->id);

        $this->assertNotEmpty(current_public_administration());
    }

    public function testCurrentPublicAdministrationNotExists(): void
    {
        auth()->login($this->user);
        $this->assertNull(current_public_administration());
    }
}
