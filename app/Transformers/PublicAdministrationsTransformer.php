<?php

namespace App\Transformers;

use App\Enums\UserRole;
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

        $statusPublicAdministrationUser = $publicAdministration->pivot
            ? $authUser->getStatusforPublicAdministration($publicAdministration)
            : null;
        $emailPublicAdministrationUser = $publicAdministration->pivot
            ? $publicAdministration->pivot->user_email
            : null;

        $data = [
            'name' => [
                'display' => implode('', [
                    '<span>',
                    '<strong>' . e($publicAdministration->name) . '</strong>',
                    '</span>',
                    $publicAdministration->is_custom ? implode('', [
                        '<span class="badge pa-type play ml-2">',
                        strtoupper(__('play')),
                        '</span>',
                    ]) : null,
                ]),
                'raw' => e($publicAdministration->name),
            ],
            'email' => e($emailPublicAdministrationUser),
            'status' => [
                'display' => '<span class="badge pa-status ' . strtolower($publicAdministration->status->key) . '">' . strtoupper($publicAdministration->status->description) . '</span>',
                'raw' => $publicAdministration->status->description,
            ],
            'user_status' => [
                'display' => '<span class="badge user-status ' . strtolower(optional($statusPublicAdministrationUser)->key) . '">' . strtoupper(optional($statusPublicAdministrationUser)->description) . '</span>',
                'raw' => optional($statusPublicAdministrationUser)->description,
            ],
            'websites_total' => $publicAdministration->websites_count,
            'websites_active' => $publicAdministration->websites_active_count,
            'added_at' => $publicAdministration->created_at->format('d/m/Y'),
            'buttons' => [],
        ];

        if (optional($statusPublicAdministrationUser)->is(UserStatus::INVITED)) {
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

        $selectPublicAdministrationRoute = route('publicAdministrations.select', [
            'public-administration' => $publicAdministration,
        ]);

        if ($authUser->isA(UserRole::SUPER_ADMIN)) {
            $selectPublicAdministrationRoute = route('admin.publicAdministrations.select', [
                'public-administration' => $publicAdministration,
                'target-route-pa-param' => true,
            ]);
        }

        if (optional($statusPublicAdministrationUser)->is(UserStatus::ACTIVE)
            || optional($statusPublicAdministrationUser)->is(UserStatus::PENDING)
            || $authUser->isA(UserRole::SUPER_ADMIN)) {
            $data['buttons'][] = [
                'link' => $selectPublicAdministrationRoute,
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
