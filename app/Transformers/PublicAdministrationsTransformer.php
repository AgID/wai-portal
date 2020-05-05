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

        $statusPublicAdministrationUser = UserStatus::coerce(intval($publicAdministration->pivot->user_status));
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
            'city' => $publicAdministration->city,
            'email' => e($emailPublicAdministrationUser),
            'region' => $publicAdministration->region,
            'status' => [
                'display' => '<span class="badge user-public-administration-status ' . strtolower($statusPublicAdministrationUser->key) . '">' . strtoupper($statusPublicAdministrationUser->description) . '</span>',
                'raw' => $statusPublicAdministrationUser->description,
            ],
            'buttons' => [],
        ];

        if ($statusPublicAdministrationUser->is(UserStatus::INVITED)) {
            $data['buttons'][] = [
                'link' => route('publicAdministration.activate', ['uuid' => $authUser->uuid, 'publicAdministration' => $publicAdministration->ipa_code]),
                'color' => 'primary',
                'label' => __('conferma'),
                'dataAttributes' => [
                    'name' => e($publicAdministration->name),
                    'type' => 'paActivation',
                    'ajax' => true,
                ],
            ];
        }
        if ($statusPublicAdministrationUser->is(UserStatus::ACTIVE)) {
            $data['buttons'][] = [
                'link' => route('publicAdministrations.change.and.redirect', ['public-administration-nav' => $publicAdministration->id]),
                'color' => 'outline-primary',
                'icon' => 'it-arrow-right',
                'label' => __('vai'),
                'dataAttributes' => [
                    'name' => e($publicAdministration->name),
                    'type' => 'paSelectTenant',
                    'ajax' => true,
                ],
            ];
        }

        return $data;
    }
}
