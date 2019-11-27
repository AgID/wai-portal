<?php

namespace Tests\Feature;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\PublicAdministration;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade as Bouncer;
use Tests\TestCase;

class UserTransformerTest extends TestCase
{
    use RefreshDatabase;

    private $user;

    private $publicAdministration;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->state('active')->create();
        $this->publicAdministration = factory(PublicAdministration::class)->state('active')->create();
        $this->publicAdministration->users()->sync($this->user->id);

        Bouncer::dontCache();
        Bouncer::scope()->onceTo($this->publicAdministration->id, function () {
            $this->user->assign(UserRole::ADMIN);
            $this->user->allow(UserPermission::MANAGE_USERS);
        });
    }

    public function testUserTransformTestAsDelegate(): void
    {
        $user = factory(User::class)->state('active')->create();
        $this->publicAdministration->users()->syncWithoutDetaching($user->id);
        Bouncer::scope()->onceTo($this->publicAdministration->id, function () use ($user) {
            $user->assign(UserRole::DELEGATED);
        });

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
                    'buttons',
                    'icons',
                ],
            ],
        ];

        $this->actingAs($user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
            ])
            ->get(route('users.data.json'))
            ->assertJsonStructure($expected)
            ->assertJsonMissing(['link' => route('users.show', ['user' => $this->user])])
            ->assertJsonMissing(['link' => route('users.edit', ['user' => $this->user])])
            ->assertJsonMissing(['link' => route('users.reactivate', ['user' => $this->user])])
            ->assertJsonMissing(['link' => route('users.suspend', ['user' => $this->user])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.restore', [
                'publicAdministration' => $this->publicAdministration,
                'trashed_user' => $this->user,
            ])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.delete', [
                'publicAdministration' => $this->publicAdministration,
                'user' => $this->user,
            ])]);
    }

    public function testUserTransformTestAsAdmin(): void
    {
        $userInvited = factory(User::class)->state('invited')->create();
        $userActive = factory(User::class)->state('active')->create();
        $userSuspended = factory(User::class)->state('suspended')->create();
        $userTrashed = factory(User::class)->state('active')->create([
            'deleted_at' => now(),
        ]);
        $this->publicAdministration->users()->syncWithoutDetaching([$userInvited->id, $userActive->id, $userSuspended->id, $userTrashed->id]);
        Bouncer::scope()->onceTo($this->publicAdministration->id, function () use ($userInvited, $userActive, $userSuspended, $userTrashed) {
            $userInvited->assign(UserRole::DELEGATED);
            $userActive->assign(UserRole::DELEGATED);
            $userSuspended->assign(UserRole::DELEGATED);
            $userTrashed->assign(UserRole::DELETED);
        });

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
                    'buttons',
                    'icons',
                ],
            ],
        ];

        $this->actingAs($this->user)
            ->withSession([
                'spid_sessionIndex' => 'fake-session-index',
                'tenant_id' => $this->publicAdministration->id,
            ])
            ->get(route('users.data.json'))
            ->assertJsonStructure($expected)
            ->assertJsonMissing(['link' => route('users.suspend', ['user' => $userInvited])])
            ->assertJsonMissing(['link' => route('users.reactivate', ['user' => $userInvited])])
            ->assertJsonFragment(['link' => route('users.show', ['user' => $userInvited])])
            ->assertJsonFragment(['link' => route('users.show', ['user' => $userActive])])
            ->assertJsonFragment(['link' => route('users.show', ['user' => $userSuspended])])
            ->assertJsonMissing(['link' => route('users.show', ['user' => $userTrashed])])
            ->assertJsonFragment(['link' => route('users.edit', ['user' => $userInvited])])
            ->assertJsonFragment(['link' => route('users.edit', ['user' => $userActive])])
            ->assertJsonFragment(['link' => route('users.edit', ['user' => $userSuspended])])
            ->assertJsonMissing(['link' => route('users.edit', ['user' => $userTrashed])])
            ->assertJsonMissing(['link' => route('users.suspend', ['user' => $userInvited])])
            ->assertJsonMissing(['link' => route('users.reactivate', ['user' => $userInvited])])
            ->assertJsonFragment(['link' => route('users.suspend', ['user' => $userActive])])
            ->assertJsonMissing(['link' => route('users.reactivate', ['user' => $userActive])])
            ->assertJsonMissing(['link' => route('users.suspend', ['user' => $userSuspended])])
            ->assertJsonFragment(['link' => route('users.reactivate', ['user' => $userSuspended])])
            ->assertJsonMissing(['link' => route('users.suspend', ['user' => $userTrashed])])
            ->assertJsonMissing(['link' => route('users.reactivate', ['user' => $userTrashed])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.restore', [
                'publicAdministration' => $this->publicAdministration,
                'trashed_user' => $userInvited,
            ])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.delete', [
                'publicAdministration' => $this->publicAdministration,
                'user' => $userInvited,
            ])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.restore', [
                'publicAdministration' => $this->publicAdministration,
                'trashed_user' => $userActive,
            ])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.delete', [
                'publicAdministration' => $this->publicAdministration,
                'user' => $userActive,
            ])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.restore', [
                'publicAdministration' => $this->publicAdministration,
                'trashed_user' => $userSuspended,
            ])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.delete', [
                'publicAdministration' => $this->publicAdministration,
                'user' => $userSuspended,
            ])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.restore', [
                'publicAdministration' => $this->publicAdministration,
                'trashed_user' => $userTrashed,
            ])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.delete', [
                'publicAdministration' => $this->publicAdministration,
                'user' => $userTrashed,
            ])]);
    }

    public function testUserTransformAsSuperAdmin(): void
    {
        $superAdmin = factory(User::class)->state('active')->create();

        Bouncer::scope()->onceTo(0, function () use ($superAdmin) {
            $superAdmin->assign(UserRole::SUPER_ADMIN);
            $superAdmin->allow(UserPermission::ACCESS_ADMIN_AREA);
        });

        $userPending = factory(User::class)->state('pending')->create();
        $userInvited = factory(User::class)->state('invited')->create();
        $userActive = factory(User::class)->state('active')->create();
        $userSuspended = factory(User::class)->state('suspended')->create();
        $userTrashed = factory(User::class)->state('active')->create([
                'deleted_at' => now(),
            ]
        );
        $this->publicAdministration->users()->syncWithoutDetaching([$userPending->id, $userInvited->id, $userActive->id, $userSuspended->id, $userTrashed->id]);
        Bouncer::scope()->onceTo($this->publicAdministration->id, function () use ($userPending, $userInvited, $userActive, $userSuspended, $userTrashed) {
            $userPending->assign(UserRole::DELEGATED);
            $userInvited->assign(UserRole::DELEGATED);
            $userActive->assign(UserRole::ADMIN);
            $userSuspended->assign(UserRole::DELEGATED);
            $userTrashed->assign(UserRole::DELETED);
        });

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
                    'status', //NOTE: trashed users don't have status
                    'buttons',
                    'icons',
                ],
            ],
        ];

        $this->actingAs($superAdmin)
            ->get(route('admin.publicAdministration.users.data.json', ['publicAdministration' => $this->publicAdministration]))
            ->assertJsonStructure($expected)
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.suspend', ['publicAdministration' => $this->publicAdministration, 'user' => $userInvited])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.reactivate', ['publicAdministration' => $this->publicAdministration, 'user' => $userInvited])])
            ->assertJsonFragment(['link' => route('admin.publicAdministration.users.show', ['publicAdministration' => $this->publicAdministration, 'user' => $userInvited])])
            ->assertJsonFragment(['link' => route('admin.publicAdministration.users.show', ['publicAdministration' => $this->publicAdministration, 'user' => $userActive])])
            ->assertJsonFragment(['link' => route('admin.publicAdministration.users.show', ['publicAdministration' => $this->publicAdministration, 'user' => $userSuspended])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.show', ['publicAdministration' => $this->publicAdministration, 'user' => $userTrashed])])
            ->assertJsonFragment(['link' => route('admin.publicAdministration.users.edit', ['publicAdministration' => $this->publicAdministration, 'user' => $userInvited])])
            ->assertJsonFragment(['link' => route('admin.publicAdministration.users.edit', ['publicAdministration' => $this->publicAdministration, 'user' => $userActive])])
            ->assertJsonFragment(['link' => route('admin.publicAdministration.users.edit', ['publicAdministration' => $this->publicAdministration, 'user' => $userSuspended])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.edit', ['publicAdministration' => $this->publicAdministration, 'user' => $userTrashed])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.suspend', ['publicAdministration' => $this->publicAdministration, 'user' => $userInvited])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.reactivate', ['publicAdministration' => $this->publicAdministration, 'user' => $userInvited])])
            ->assertJsonFragment(['link' => route('admin.publicAdministration.users.suspend', ['publicAdministration' => $this->publicAdministration, 'user' => $userActive])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.reactivate', ['publicAdministration' => $this->publicAdministration, 'user' => $userActive])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.suspend', ['publicAdministration' => $this->publicAdministration, 'user' => $userSuspended])])
            ->assertJsonFragment(['link' => route('admin.publicAdministration.users.reactivate', ['publicAdministration' => $this->publicAdministration, 'user' => $userSuspended])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.suspend', ['publicAdministration' => $this->publicAdministration, 'user' => $userTrashed])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.reactivate', ['publicAdministration' => $this->publicAdministration, 'user' => $userTrashed])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.restore', [
                'publicAdministration' => $this->publicAdministration,
                'trashed_user' => $userInvited,
            ])])
            ->assertJsonFragment(['link' => route('admin.publicAdministration.users.delete', [
                'publicAdministration' => $this->publicAdministration,
                'user' => $userInvited,
            ])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.restore', [
                'publicAdministration' => $this->publicAdministration,
                'trashed_user' => $userActive,
            ])])
            ->assertJsonFragment(['link' => route('admin.publicAdministration.users.delete', [
                'publicAdministration' => $this->publicAdministration,
                'user' => $userActive,
            ])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.restore', [
                'publicAdministration' => $this->publicAdministration,
                'trashed_user' => $userSuspended,
            ])])
            ->assertJsonFragment(['link' => route('admin.publicAdministration.users.delete', [
                'publicAdministration' => $this->publicAdministration,
                'user' => $userSuspended,
            ])])
            ->assertJsonFragment(['link' => route('admin.publicAdministration.users.restore', [
                'publicAdministration' => $this->publicAdministration,
                'trashed_user' => $userTrashed,
            ])])
            ->assertJsonMissing(['link' => route('admin.publicAdministration.users.delete', [
                'publicAdministration' => $this->publicAdministration,
                'user' => $userTrashed,
            ])]);
    }
}
