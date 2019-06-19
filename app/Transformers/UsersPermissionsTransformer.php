<?php

namespace App\Transformers;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\User;
use League\Fractal\TransformerAbstract;

class UsersPermissionsTransformer extends TransformerAbstract
{
    /**
     * @param User $user
     *
     * @return array
     */
    public function transform(User $user)
    {
        $website = request()->route('website');
        $readOnly = request()->filled('readOnly');

        $data = [
            'name' => implode(' ', [$user->familyName, $user->name]),
            'email' => $user->email,
            'added_at' => $user->created_at->format('d/m/Y'),
            'status' => $user->status->description,
            'checkboxes' => [
                [
                    'name' => 'usersEnabled[' . $user->id . ']',
                    'value' => 'enabled',
                    'disabled' => $readOnly || $user->isAn(UserRole::ADMIN),
                    'checked' => $user->isAn(UserRole::ADMIN) || (isset($website) && $user->can(UserPermission::READ_ANALYTICS, $website)),
                    'dataAttributes' => [
                        'user' => $user->id,
                    ],
                ],
            ],
            'radios' => [
                [
                    'name' => 'usersPermissions[' . $user->id . ']',
                    'value' => UserPermission::READ_ANALYTICS,
                    'label' => UserPermission::getDescription(UserPermission::READ_ANALYTICS),
                    'disabled' => $readOnly || $user->isAn(UserRole::ADMIN),
                    'checked' => (!isset($website) && !$user->isA(UserRole::ADMIN)) || (isset($website) && $user->can(UserPermission::READ_ANALYTICS, $website) && $user->cannot(UserPermission::MANAGE_ANALYTICS, $website)),
                    'dataAttributes' => [
                        'user' => $user->id,
                    ],
                ],
                [
                    'name' => 'usersPermissions[' . $user->id . ']',
                    'value' => UserPermission::MANAGE_ANALYTICS,
                    'label' => UserPermission::getDescription(UserPermission::MANAGE_ANALYTICS),
                    'disabled' => $readOnly || $user->isAn(UserRole::ADMIN),
                    'checked' => $user->isAn(UserRole::ADMIN) || (isset($website) && $user->can(UserPermission::MANAGE_ANALYTICS, $website)),
                    'dataAttributes' => [
                        'user' => $user->id,
                    ],
                ],
            ],
            'control' => '',
        ];

        return $data;
    }
}
