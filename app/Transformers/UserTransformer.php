<?php

namespace App\Transformers;

use App\Enums\UserStatus;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * @param \App\Models\User $user
     *
     * @return array
     */
    public function transform(User $user)
    {
        $data = [
            'name' => $user->name,
            'familyName' => $user->familyName,
            'email' => $user->email,
            'role' => __('auth.roles.' . $user->roles()->first()->name),
            'added_at' => $user->created_at->format('d/m/Y'),
            'status' => UserStatus::getDescription($user->status),
            'actions' => [],
            'control' => '',
        ];

        if (auth()->user()->can('manage-users')) {
            $data['actions'][] = [
                'link' => route('users-edit', ['user' => $user], false),
                'label' => __('ui.pages.users.index.edit_user'),
            ];
        }

        return $data;
    }
}
