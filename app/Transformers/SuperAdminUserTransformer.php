<?php

namespace App\Transformers;

use App\Enums\UserStatus;
use App\Models\User;
use League\Fractal\TransformerAbstract;

/**
 * Super admin user transformer.
 */
class SuperAdminUserTransformer extends TransformerAbstract
{
    /**
     * Transform the super admin user for datatable.
     *
     * @param User $user the user
     *
     * @return array the response
     */
    public function transform(User $user): array
    {
        $data = [
            'name' => [
                'display' => implode('', [
                    '<div class="d-flex align-items-center">',
                    '<div class="avatar size-lg mr-2">',
                    implode('', [
                        '<img src="https://www.gravatar.com/avatar/' . md5(strtolower(trim(e($user->email)))) . '?d=' . (e($user->email) ? 'identicon' : 'mp') . '"',
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
            'email' => e($user->email),
            'added_at' => $user->created_at->format('d/m/Y'),
            'status' => [
                'display' => '<span class="badge user-status ' . strtolower($user->status->key) . '">' . strtoupper($user->status->description) . '</span>',
                'raw' => $user->status->description,
            ],
            'buttons' => [
                [
                    'link' => route('admin.users.show', ['user' => $user]),
                    'label' => __('dettagli'),
                ],
            ],
            'icons' => [
                [
                    'icon' => 'it-pencil',
                    'link' => route('admin.users.edit', ['user' => $user]),
                    'color' => 'primary',
                    'title' => __('modifica'),
                ],
            ],
        ];

        if ($user->status->is(UserStatus::SUSPENDED)) {
            $data['icons'][] = [
                'icon' => 'it-exchange-circle',
                'link' => route('admin.users.reactivate', ['user' => $user]),
                'color' => 'primary',
                'title' => __('riattiva'),
                'dataAttributes' => [
                    'user-name' => e($user->full_name),
                    'type' => 'userSuspendReactivate',
                    'current-status-description' => $user->status->description,
                    'current-status' => $user->status->key,
                    'ajax' => true,
                ],
            ];
        } elseif ($user->uuid !== auth()->user()->uuid && !$user->isTheLastActiveSuperAdministrator()) {
            $data['icons'][] = [
                'icon' => 'it-close-circle',
                'link' => route('admin.users.suspend', ['user' => $user]),
                'color' => 'primary',
                'title' => __('sospendi'),
                'dataAttributes' => [
                    'user-name' => e($user->full_name),
                    'type' => 'userSuspendReactivate',
                    'current-status-description' => $user->status->description,
                    'current-status' => $user->status->key,
                    'ajax' => true,
                ],
            ];
        }

        return $data;
    }
}
