<?php

namespace App\Transformers;

use App\Enums\UserPermission;
use App\Enums\UserRole;
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
        $isAdmin = $user->isAn(UserRole::ADMIN);
        if ($publicAdministration = request()->route('publicAdministration')) {
            $isAdmin = Bouncer::scope()->onceTo($publicAdministration->id, function () use ($user) {
                return $user->isA(UserRole::ADMIN);
            });
        }
        $data = [
            'name' => implode(' ', [$user->familyName, $user->name]),
            'email' => $user->email,
            'admin' => $isAdmin,
            'added_at' => $user->created_at->format('d/m/Y'),
            'status' => $user->status->description,
            'buttons' => [],
            'control' => '',
        ];

        if (auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA)) {
            $data['buttons'][] = [
                'link' => route('admin.publicAdministration.users.show', ['publicAdministration' => request()->route('publicAdministration'), 'user' => $user], false),
                'label' => __('ui.pages.users.index.show_user'),
            ];
            $data['buttons'][] = [
                'link' => route('admin.publicAdministration.users.edit', ['publicAdministration' => request()->route('publicAdministration'), 'user' => $user], false),
                'label' => __('ui.pages.users.index.edit_user'),
            ];
            if (!$user->status->is(UserStatus::PENDING)) {
                if ($user->trashed()) {
                    $data['buttons'][] = [
                        'link' => route('admin.publicAdministration.users.restore', ['publicAdministration' => request()->route('publicAdministration'), 'user' => $user], false),
                        'label' => __('ui.pages.users.index.restore_user'),
                        'dataAttributes' => [
                            'type' => 'deleteStatus',
                        ],
                    ];
                } else {
                    $data['buttons'][] = [
                        'link' => route('admin.publicAdministration.users.delete', ['publicAdministration' => request()->route('publicAdministration'), 'user' => $user], false),
                        'label' => __('ui.pages.users.index.delete_user'),
                        'dataAttributes' => [
                            'type' => 'deleteStatus',
                        ],
                    ];
                }
            }
        } elseif (auth()->user()->can(UserPermission::MANAGE_USERS)) {
            $data['buttons'][] = [
                'link' => route('users.show', ['user' => $user], false),
                'label' => __('ui.pages.users.index.show_user'),
            ];
            $data['buttons'][] = [
                'link' => route('users.edit', ['user' => $user], false),
                'label' => __('ui.pages.users.index.edit_user'),
            ];
            if ($user->status->is(UserStatus::SUSPENDED)) {
                $data['buttons'][] = [
                    'link' => route('users.reactivate', ['user' => $user], false),
                    'label' => __('ui.pages.users.index.reactivate_user'),
                    'dataAttributes' => [
                        'type' => 'suspendStatus',
                    ],
                ];
            } else {
                $data['buttons'][] = [
                    'link' => route('users.suspend', ['user' => $user], false),
                    'label' => __('ui.pages.users.index.suspend_user'),
                    'dataAttributes' => [
                        'type' => 'suspendStatus',
                    ],
                ];
            }
        }

        return $data;
    }
}
