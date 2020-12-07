<?php

namespace App\Transformers;

use App\Enums\UserPermission;
use App\Models\Key;
use League\Fractal\TransformerAbstract;

/**
 * Key transformer.
 */
class KeysTransformer extends TransformerAbstract
{
    /**
     * Transform the api keys for datatable.
     *
     * @param keys $keys
     *
     * @return array the response
     */
    public function transform(Key $keys): array
    {
        $authUser = auth()->user();

        $authUserCanAccessAdminArea = $authUser->can(UserPermission::ACCESS_ADMIN_AREA);

        $data = [
            'client_name' => [
                'display' => implode('', [
                    '<span>',
                    '<strong>' . e($keys->client_name) . '</strong>',
                    '</span>',
                ]),
                'raw' => e($keys->client_name),
            ],
            'consumer_id' => $keys->consumer_id,
            'added_at' => $keys->created_at->format('d/m/Y'),
            'icons' => [],
            'buttons' => [],
        ];

        $data['buttons'][] = [
            'link' => route('api-key.show', ['key' => $keys->consumer_id]),
            'color' => 'outline-primary',
            'label' => __('dettagli'),
        ];

        if ($authUser->can(UserPermission::MANAGE_WEBSITES)) {
            $data['icons'][] = [
                'icon' => 'it-pencil',
                'link' => route('api-key.edit', ['key' => $keys->consumer_id]),
                'color' => 'primary',
                'title' => __('modifica'),
            ];
        }

        if ($authUserCanAccessAdminArea) {
            $data['buttons'][] = [
                'link' => route('api-key.delete', ['key' => $keys->consumer_id]),
                'label' => __('elimina'),
                'color' => 'danger',
                /* 'dataAttributes' => [
                    'website-name' => e($website->name),
                    'type' => 'websiteDeleteRestore',
                    'ajax' => true,
                ], */
            ];
        }

        return $data;
    }
}
