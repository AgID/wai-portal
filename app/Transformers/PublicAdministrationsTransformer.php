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

        $status = UserStatus::coerce($publicAdministration->pivot->pa_status);
        $data = [
            'name' => [
                'display' => implode('', [
                    '<span>',
                    '<strong>' . e($publicAdministration->name) . '</strong>',
                    '<br>',
                    '<small>' . e($publicAdministration->type) . '</small>',
                    '</span>',
                ]),
                'raw' => e($publicAdministration->name),
            ],
            'city' => $publicAdministration->city,
            'region' => $publicAdministration->region,
            'status' => [
                'display' => '<span class="badge website-status ' . strtolower($status->key) . '">' . strtoupper($status->description) . '</span>',
                'raw' => $status->description,
            ],
            'buttons' => [],
        ];

        if ($status->is(UserStatus::INVITED)) {
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
