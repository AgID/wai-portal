<?php

namespace App\Transformers;

use App\Enums\UserPermission;
use App\Models\Credential;
use League\Fractal\TransformerAbstract;

/**
 * Credential transformer.
 */
class CredentialsTransformer extends TransformerAbstract
{
    /**
     * Transform the api credentials for datatable.
     *
     * @param credentials $credentials
     *
     * @return array the response
     */
    public function transform(Credential $credentials): array
    {
        $authUser = auth()->user();

        $data = [
            'client_name' => [
                'display' => implode('', [
                    '<span>',
                    '<strong>' . e($credentials->client_name) . '</strong>',
                    '</span>',
                ]),
                'raw' => e($credentials->client_name),
            ],
            'consumer_id' => $credentials->consumer_id,
            'added_at' => $credentials->created_at->format('d/m/Y'),
            'icons' => [],
            'buttons' => [],
        ];

        $data['buttons'][] = [
            'link' => route('api-credential.show', ['credential' => $credentials->consumer_id]),
            'color' => 'outline-primary',
            'label' => __('dettagli'),
        ];

        if ($authUser->can(UserPermission::MANAGE_WEBSITES)) {
            $data['icons'][] = [
                'icon' => 'it-pencil',
                'link' => route('api-credential.edit', ['credential' => $credentials->consumer_id]),
                'color' => 'primary',
                'title' => __('modifica'),
            ];
        }

        $data['buttons'][] = [
                'link' => route('api-credential.delete', ['credential' => $credentials->consumer_id]),
                'label' => __('elimina'),
                'color' => 'danger',
                'dataAttributes' => [
                    'credentialName' => e($credentials->client_name),
                    'type' => 'credentialDelete',
                    'ajax' => true,
                ],
            ];

        return $data;
    }
}
