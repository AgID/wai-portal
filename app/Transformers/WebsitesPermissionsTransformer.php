<?php

namespace App\Transformers;

use App\Enums\UserPermission;
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
                    'checked' => true,
                    'disabled' => true,
                    'type' => 'websitePermission',
                    'dataAttributes' => [
                        'website' => $website->id,
                    ],
                ],
                [
                    'name' => 'websitesPermissions[' . $website->id . ']',
                    'value' => UserPermission::MANAGE_ANALYTICS,
                    'label' => UserPermission::getDescription(UserPermission::MANAGE_ANALYTICS),
                    'disabled' => true,
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
