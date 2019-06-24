<?php

namespace App\Transformers;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Models\Website;
use League\Fractal\TransformerAbstract;

class WebsitesPermissionsTransformer extends TransformerAbstract
{
    /**
     * @param Website $website
     *
     * @return array
     */
    public function transform(Website $website)
    {
        $user = request()->route('user');
        $readOnly = request()->filled('readOnly');

        $data = [
            'url' => '<a href="http://' . $website->url . '">' . $website->url . '</a>',
            'type' => $website->type->description,
            'added_at' => $website->created_at->format('d/m/Y'),
            'status' => $website->status->description,
            'checkboxes' => [
                [
                    'name' => 'websitesEnabled[' . $website->id . ']',
                    'value' => 'enabled',
                    'type' => 'websiteEnabled',
                    'disabled' => $readOnly || (isset($user) && $user->isA(UserRole::ADMIN)),
                    'checked' => isset($user) && (!$user->isA(UserRole::ADMIN)) && ($user->can(UserPermission::MANAGE_ANALYTICS, $website) || $user->can(UserPermission::READ_ANALYTICS, $website)),
                    'dataAttributes' => [
                        'website' => $website->id,
                    ],
                ],
            ],
            'radios' => [
                [
                    'name' => 'websitesPermissions[' . $website->id . ']',
                    'value' => UserPermission::READ_ANALYTICS,
                    'label' => UserPermission::getDescription(UserPermission::READ_ANALYTICS),
                    'checked' => !isset($user) || (!$user->isA(UserRole::ADMIN) && $user->can(UserPermission::READ_ANALYTICS, $website) && $user->cannot(UserPermission::MANAGE_ANALYTICS, $website)),
                    'disabled' => $readOnly || !isset($user) || $user->isA(UserRole::ADMIN),
                    'type' => 'websitePermission',
                    'dataAttributes' => [
                        'website' => $website->id,
                    ],
                ],
                [
                    'name' => 'websitesPermissions[' . $website->id . ']',
                    'value' => UserPermission::MANAGE_ANALYTICS,
                    'label' => UserPermission::getDescription(UserPermission::MANAGE_ANALYTICS),
                    'disabled' => $readOnly || !isset($user) || $user->isA(UserRole::ADMIN),
                    'checked' => isset($user) && !$user->isA(UserRole::ADMIN) && $user->can(UserPermission::MANAGE_ANALYTICS, $website),
                    'type' => 'websitePermission',
                    'dataAttributes' => [
                        'website' => $website->id,
                    ],
                ],
            ],
            'control' => '',
        ];

        return $data;
    }
}
