<?php

namespace App\Transformers;

use App\Enums\UserPermission;
use App\Enums\UserStatus;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Models\Website;
use League\Fractal\TransformerAbstract;

/**
 * Website transformer.
 */
class WebsiteTransformer extends TransformerAbstract
{
    /**
     * Transform the website for datatable.
     *
     * @param Website $website the website
     *
     * @return array the response
     */
    public function transform(Website $website): array
    {
        $publicAdministration = request()->route('publicAdministration');
        $authUser = auth()->user();
        $authUserCanAccessAdminArea = $authUser->can(UserPermission::ACCESS_ADMIN_AREA);
        $websiteUrlLink = array_key_exists('scheme', parse_url($website->url))
            ? e($website->url)
            : 'http://' . e($website->url);

        $data = [
            'website_name' => [
                'display' => implode('', [
                    '<span>',
                    '<strong>' . e($website->name) . '</strong>',
                    '<br>',
                    '<small><a href="' . $websiteUrlLink . '" target="_blank">' . e($website->url) . '</a></small>',
                    '</span>',
                ]),
                'raw' => e($website->name),
            ],
            'type' => $website->type->description,
            'added_at' => $website->created_at->format('d/m/Y'),
            'status' => [
                'display' => '<span class="badge website-status ' . strtolower($website->status->key) . '">' . strtoupper($website->status->description) . '</span>',
                'raw' => $website->status->description,
            ],
            'icons' => [],
            'buttons' => [],
        ];

        if (!$website->trashed()) {
            $data['buttons'][] = [
                'link' => $authUserCanAccessAdminArea
                    ? route('admin.publicAdministration.websites.show', [
                        'publicAdministration' => $publicAdministration,
                        'website' => $website,
                    ])
                    : route('websites.show', ['website' => $website]),
                'color' => 'outline-primary',
                'label' => __('dettagli'),
            ];

            if ($website->status->is(WebsiteStatus::PENDING)) {
                if ($authUser->can(UserPermission::MANAGE_WEBSITES) || $authUser->publicAdministrations()->where('pa_status', UserStatus::PENDING)->first()) {
                    $data['icons'][] = [
                        'icon' => 'it-plug',
                        'link' => $authUserCanAccessAdminArea
                            ? route('admin.publicAdministration.websites.tracking.check', [
                                'publicAdministration' => $publicAdministration,
                                'website' => $website,
                            ])
                            : route('websites.tracking.check', ['website' => $website->slug]),
                        'color' => 'primary',
                        'title' => __('verifica attivazione'),
                        'dataAttributes' => [
                            'website-name' => e($website->name),
                            'type' => 'checkTracking',
                            'ajax' => true,
                        ],
                    ];
                }
            }

            if ($authUser->can(UserPermission::MANAGE_WEBSITES)) {
                $data['icons'][] = [
                    'icon' => 'it-pencil',
                    'link' => $authUserCanAccessAdminArea
                        ? route('admin.publicAdministration.websites.edit', [
                            'publicAdministration' => $publicAdministration,
                            'website' => $website,
                        ])
                        : route('websites.edit', ['website' => $website]),
                    'color' => 'primary',
                    'title' => __('modifica'),
                ];

                if (!$website->type->is(WebsiteType::INSTITUTIONAL)) {
                    if ($website->status->is(WebsiteStatus::ACTIVE)) {
                        $data['buttons'][] = [
                            'link' => $authUserCanAccessAdminArea
                                ? route('admin.publicAdministration.websites.archive', [
                                    'publicAdministration' => $publicAdministration,
                                    'website' => $website,
                                ])
                                : route('websites.archive', ['website' => $website]),
                            'color' => 'outline-primary',
                            'label' => __('archivia'),
                            'dataAttributes' => [
                                'website-name' => e($website->name),
                                'type' => 'websiteArchiveUnarchive',
                                'current-status-description' => $website->status->description,
                                'current-status' => $website->status->key,
                                'ajax' => true,
                            ],
                        ];
                    } elseif ($website->status->is(WebsiteStatus::ARCHIVED)) {
                        $data['buttons'][] = [
                            'link' => $authUserCanAccessAdminArea
                                ? route('admin.publicAdministration.websites.unarchive', [
                                    'publicAdministration' => $publicAdministration,
                                    'website' => $website,
                                ])
                                : route('websites.unarchive', ['website' => $website]),
                            'color' => 'outline-primary',
                            'label' => __('riattiva'),
                            'dataAttributes' => [
                                'website-name' => e($website->name),
                                'type' => 'websiteArchiveUnarchive',
                                'current-status-description' => $website->status->description,
                                'current-status' => $website->status->key,
                                'ajax' => true,
                            ],
                        ];
                    }
                }
            }

            if (!$website->status->is(WebsiteStatus::PENDING) && $authUser->can(UserPermission::READ_ANALYTICS, $website)) {
                if (!$authUserCanAccessAdminArea) {
                    $data['buttons'][] = [
                        'icon' => 'it-arrow-right',
                        'iconColor' => 'white',
                        'link' => route('analytics.service.login', ['websiteAnalyticsId' => $website->analytics_id]),
                        'color' => 'primary',
                        'label' => __('dashboard'),
                    ];
                }
            }
        }

        if ($authUserCanAccessAdminArea) {
            if (!$website->type->is(WebsiteType::INSTITUTIONAL)) {
                if ($website->trashed()) {
                    $data['status'] = '';
                    $data['trashed'] = true;
                    $data['buttons'][] = [
                        'link' => route('admin.publicAdministration.websites.restore', [
                            'publicAdministration' => $publicAdministration,
                            'trashed_website' => $website,
                        ]),
                        'label' => __('ripristina'),
                        'color' => 'warning',
                        'dataAttributes' => [
                            'website-name' => e($website->name),
                            'type' => 'websiteDeleteRestore',
                            'trashed' => true,
                            'ajax' => true,
                        ],
                    ];
                } else {
                    $data['buttons'][] = [
                        'link' => route('admin.publicAdministration.websites.delete', [
                            'publicAdministration' => $publicAdministration,
                            'website' => $website,
                        ]),
                        'label' => __('elimina'),
                        'color' => 'danger',
                        'dataAttributes' => [
                            'website-name' => e($website->name),
                            'type' => 'websiteDeleteRestore',
                            'ajax' => true,
                        ],
                    ];
                }
            }
        }

        return $data;
    }
}
