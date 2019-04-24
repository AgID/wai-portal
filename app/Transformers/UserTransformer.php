<?php

namespace App\Transformers;

use App\Enums\UserPermission;
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
            'admin' => $user->isAn('admin'),
            'added_at' => $user->created_at->format('d/m/Y'),
            'status' => $user->status->description,
            'buttons' => [],
            'control' => '',
        ];

        if (auth()->user()->can(UserPermission::MANAGE_USERS)) {
            $data['buttons'][] = [
                'link' => route('users-edit', ['user' => $user], false),
                'label' => __('ui.pages.users.index.edit_user'),
            ];
        }

        return $data;
    }
}
