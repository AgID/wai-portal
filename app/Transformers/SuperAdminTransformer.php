<?php

namespace App\Transformers;

use App\Enums\UserStatus;
use App\Models\User;
use League\Fractal\TransformerAbstract;

/**
 * Super-admin user transformer.
 */
class SuperAdminTransformer extends TransformerAbstract
{
    /**
     * Transform the super-admin user for datatable.
     *
     * @param User $user the user
     *
     * @return array the response
     */
    public function transform(User $user): array
    {
        $data = [
            'name' => implode(' ', [$user->familyName, $user->name]),
            'email' => $user->email,
            'added_at' => $user->created_at->format('d/m/Y'),
            'status' => $user->status->description,
            'buttons' => [
                [
                    'link' => route('admin.users.show', ['user' => $user], false),
                    'label' => __('ui.pages.admin.users.index.show_user'),
                ],
                [
                    'link' => route('admin.users.edit', ['user' => $user], false),
                    'label' => __('ui.pages.admin.users.index.edit_user'),
                ],
            ],
            'control' => '',
        ];

        if ($user->status->is(UserStatus::SUSPENDED)) {
            $data['buttons'][] = [
                'link' => route('admin.users.reactivate', ['user' => $user], false),
                'label' => __('ui.pages.admin.users.index.reactivate_user'),
                'dataAttributes' => [
                    'type' => 'suspendStatus',
                ],
            ];
        } else {
            $data['buttons'][] = [
                'link' => route('admin.users.suspend', ['user' => $user], false),
                'label' => __('ui.pages.admin.users.index.suspend_user'),
                'dataAttributes' => [
                    'type' => 'suspendStatus',
                ],
            ];
        }

        return $data;
    }
}
