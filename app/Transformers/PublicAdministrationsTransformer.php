<?php

namespace App\Transformers;

use App\Enums\UserStatus;
use App\Models\PublicAdministration;
use App\Models\Website;
use League\Fractal\TransformerAbstract;

/**
 * Website transformer.
 */
class PublicAdministrationsTransformer extends TransformerAbstract
{
    /**
     * Transform the website for datatable.
     *
     * @param Website $website the website
     *
     * @return array the response
     */
    public function transform(PublicAdministration $publicAdministration): array
    {
        $authUser = auth()->user();

        $statusPublicAdministrationUser = UserStatus::fromValue(intval($publicAdministration->pivot->user_status));
        $emailPublicAdministrationUser = $publicAdministration->pivot->user_email;

        $data = [
            'name' => [
                'display' => implode('', [
                    '<span>',
                    '<strong>' . e($publicAdministration->name) . '</strong>',
                    '</span>',
                ]),
                'raw' => e($publicAdministration->name),
            ],
            'email' => e($emailPublicAdministrationUser),
            'userStatus' => [
                'display' => '<span class="badge user-status ' . strtolower($statusPublicAdministrationUser->key) . '">' . strtoupper($statusPublicAdministrationUser->description) . '</span>',
                'raw' => $statusPublicAdministrationUser->description,
            ],
            'buttons' => [],
        ];

        if ($statusPublicAdministrationUser->is(UserStatus::INVITED)) {
            $data['buttons'][] = [
                'link' => route('publicAdministration.acceptInvitation', ['publicAdministration' => $publicAdministration]),
                'color' => 'primary',
                'label' => __('accetta invito'),
                'dataAttributes' => [
                    'name' => e($publicAdministration->name),
                    'type' => 'acceptInvitation',
                    'ajax' => true,
                ],
            ];
        }

        if ($statusPublicAdministrationUser->is(UserStatus::ACTIVE) || $statusPublicAdministrationUser->is(UserStatus::PENDING)) {
            $data['buttons'][] = [
                'link' => route('publicAdministrations.select', ['public-administration' => $publicAdministration]),
                'color' => 'outline-primary',
                'icon' => 'it-arrow-right',
                'label' => __('vai'),
                'dataAttributes' => [
                    'name' => e($publicAdministration->name),
                    'type' => 'selectTenant',
                ],
            ];
        }

        return $data;
    }
}
