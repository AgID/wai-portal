<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class SuperAdminUserTransformerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->state('active')->create();
        Bouncer::dontCache();
        Bouncer::scope()->onceTo(0, function () {
            $this->user->assign(UserRole::SUPER_ADMIN);
        });
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testSuperAdminUserTransform(): void
    {
        $expected = [
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                '*' => [
                    'name' => [
                        'display',
                        'raw',
                    ],
                    'email',
                    'added_at',
                    'status' => [
                        'display',
                        'raw',
                    ],
                    'buttons' => [
                        [
                            'link',
                            'label',
                        ],
                    ],
                    'icons' => [
                        [
                            'icon',
                            'link',
                            'color',
                            'title',
                        ],
                    ],
                ],
            ],
        ];

        $this->actingAs($this->user)
            ->get(route('admin.users.data.json'))
            ->assertJsonStructure($expected)
            ->assertJsonFragment(['raw' => e($this->user->full_name)])
            ->assertJsonMissing(['link' => route('admin.users.suspend', ['user' => $this->user])]);
    }

    public function testSuperAdminUserSuspendedTransform(): void
    {
        $user = factory(User::class)->state('suspended')->create();
        $user->assign(UserRole::SUPER_ADMIN);

        $this->actingAs($this->user)
            ->get(route('admin.users.data.json'))
            ->assertJsonFragment(['link' => route('admin.users.reactivate', ['user' => $user])])
            ->assertJsonMissing(['link' => route('admin.users.suspend', ['user' => $user])]);
    }

    public function testSuperAdminSuspendTransform(): void
    {
        $user = factory(User::class)->state('active')->create();
        $user->assign(UserRole::SUPER_ADMIN);

        $this->actingAs($this->user)
            ->get(route('admin.users.data.json'))
            ->assertJsonFragment(['link' => route('admin.users.suspend', ['user' => $user])])
            ->assertJsonMissing(['link' => route('admin.users.suspend', ['user' => $this->user])]);
    }
}
