<?php

namespace App\Transformers;

use App\Enums\UserPermission;
use App\Enums\UserStatus;
use App\Models\User;
use League\Fractal\TransformerAbstract;
use Silber\Bouncer\BouncerFacade as Bouncer;

/**
 * User transformer.
 */
class UserTransformer extends TransformerAbstract
{
    /**
     * Transform the user for datatable.
     *
     * @param User $user the user
     *
     * @return array the response
     */
    public function transform(User $user): array
    {
        $publicAdministration = request()->route('publicAdministration', current_public_administration());
        $authUser = auth()->user();
        $authUserCanAccessAdminArea = $authUser->can(UserPermission::ACCESS_ADMIN_AREA);

        $publicAdministrationUser = $user->publicAdministrationsWithSuspended()->where('public_administration_id', $publicAdministration->id)->first();
        $statusPublicAdministrationUser = UserStatus::coerce(intval($publicAdministrationUser->pivot->user_status));
        $emailPublicAdministrationUser = $publicAdministrationUser->pivot->user_email;

        return Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user, $publicAdministration, $authUser, $authUserCanAccessAdminArea, $statusPublicAdministrationUser, $emailPublicAdministrationUser) {
            $data = [
                'name' => [
                    'display' => implode('', [
                        '<div class="d-flex align-items-center">',
                        '<div class="avatar size-lg mr-2">',
                        implode('', [
                            '<img src="https://www.gravatar.com/avatar/' . md5(strtolower(trim(e($emailPublicAdministrationUser)))) . '?d=' . (e($emailPublicAdministrationUser) ? 'identicon' : 'mp') . '"',
                            'alt="' . e($user->full_name) . '">',
                        ]),
                        '</div>',
                        '<span class="user">',
                        '<strong>' . e($user->full_name) . '</strong>',
                        '<br>',
                        '<small class="text-muted text-uppercase">' . $user->all_role_names . '</small>',
                        '</span>',
                        '</div>',
                    ]),
                    'raw' => e($user->full_name),
                ],
                'email' => e($emailPublicAdministrationUser),
                'added_at' => $user->created_at->format('d/m/Y'),
                'status' => [
                    'display' => '<span class="badge user-status ' . strtolower($statusPublicAdministrationUser->key) . '">' . strtoupper($statusPublicAdministrationUser->description) . '</span>',
                    'raw' => $statusPublicAdministrationUser->description,
                ],
                'buttons' => [],
                'icons' => [],
            ];

            if (!$user->trashed() && ($authUser->can(UserPermission::MANAGE_USERS) || $authUserCanAccessAdminArea)) {
                $data['buttons'][] = [
                    'link' => $authUserCanAccessAdminArea
                        ? route('admin.publicAdministration.users.show', [
                            'publicAdministration' => $publicAdministration,
                            'user' => $user,
                        ])
                        : route('users.show', ['user' => $user]),
                    'color' => 'outline-primary',
                    'label' => __('dettagli'),
                ];
                $data['icons'][] = [
                    'icon' => 'it-pencil',
                    'link' => $authUserCanAccessAdminArea
                        ? route('admin.publicAdministration.users.edit', [
                            'publicAdministration' => $publicAdministration,
                            'user' => $user,
                        ])
                        : route('users.edit', ['user' => $user]),
                    'color' => 'primary',
                    'title' => __('modifica'),
                ];

                if ($statusPublicAdministrationUser->is(UserStatus::SUSPENDED)) {
                    $data['icons'][] = [
                        'icon' => 'it-exchange-circle',
                        'link' => $authUserCanAccessAdminArea
                            ? route('admin.publicAdministration.users.reactivate', [
                                'publicAdministration' => $publicAdministration,
                                'user' => $user,
                            ])
                            : route('users.reactivate', ['user' => $user]),
                        'color' => 'primary',
                        'title' => __('riattiva'),
                        'dataAttributes' => [
                            'user-name' => e($user->full_name),
                            'type' => 'userSuspendReactivate',
                            'current-status-description' => $statusPublicAdministrationUser->description,
                            'current-status' => $statusPublicAdministrationUser->key,
                            'ajax' => true,
                        ],
                    ];
                } elseif (!$statusPublicAdministrationUser->is(UserStatus::INVITED) && !$user->isTheLastActiveAdministratorOf($publicAdministration)) {
                    $data['icons'][] = [
                        'icon' => 'it-close-circle',
                        'link' => $authUserCanAccessAdminArea
                            ? route('admin.publicAdministration.users.suspend', [
                                'publicAdministration' => $publicAdministration,
                                'user' => $user,
                            ])
                            : route('users.suspend', ['user' => $user]),
                        'color' => 'primary',
                        'title' => __('sospendi'),
                        'dataAttributes' => [
                            'user-name' => e($user->full_name),
                            'type' => 'userSuspendReactivate',
                            'current-status-description' => $statusPublicAdministrationUser->description,
                            'current-status' => $statusPublicAdministrationUser->key,
                            'ajax' => true,
                        ],
                    ];
                }
            }

            if (!$statusPublicAdministrationUser->is(UserStatus::PENDING) && $authUserCanAccessAdminArea) {
                if ($user->uuid !== $authUser->uuid && !$user->isTheLastActiveAdministratorOf($publicAdministration)) {
                    $data['buttons'][] = [
                        'link' => route('admin.publicAdministration.users.delete', [
                            'publicAdministration' => $publicAdministration,
                            'user' => $user,
                        ]),
                        'label' => __('elimina'),
                        'color' => 'danger',
                        'dataAttributes' => [
                            'user-name' => e($user->full_name),
                            'type' => 'userDelete',
                            'ajax' => true,
                        ],
                    ];
                }
            }

            return $data;
        });
    }
}
