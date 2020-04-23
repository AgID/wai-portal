<?php

namespace App\Transformers;

use App\Enums\UserStatus;
use App\Models\PublicAdministration;
use App\Models\Website;
use Illuminate\Support\Facades\Hash;
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

        $statusPublicAdministrationUser = UserStatus::coerce(intval($publicAdministration->pivot->pa_status));
        $emailPublicAdministrationUser = $publicAdministration->pivot->pa_email;

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
                'display' => '<span class="badge website-status ' . strtolower($statusPublicAdministrationUser->key) . '">' . strtoupper($statusPublicAdministrationUser->description) . '</span>',
                'raw' => $statusPublicAdministrationUser->description,
            ],
            'buttons' => [],
        ];

        if ($statusPublicAdministrationUser->is(UserStatus::INVITED)) {
            $data['buttons'][] = [
                'link' => route('publicAdministration.activation', ['uuid' => $authUser->uuid, 'pa' => base64_encode(Hash::make($publicAdministration->id))]),
                'color' => 'primary',
                'label' => __('conferma'),
                'dataAttributes' => [
                    'name' => e($publicAdministration->name),
                    'type' => 'paActivation',
                    'ajax' => true,
                ],
            ];
        }

        return $data;
    }
}
