<?php

namespace App\Transformers;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * @param \App\Models\User $user
     *
     * @return array
     */
    public function transform(User $user): array
    {
        $data = [
            'name' => implode(' ', [$user->familyName, $user->name]),
            'email' => $user->email,
            'admin' => $user->isAn(UserRole::ADMIN),
            'added_at' => $user->created_at->format('d/m/Y'),
            'status' => $user->status->description,
            'buttons' => [],
            'control' => '',
        ];

        if (auth()->user()->can(UserPermission::MANAGE_USERS)) {
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
