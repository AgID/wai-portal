<?php

namespace App\Transformers;

use App\Enums\UserPermission;
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
        $data = [
            'name' => $website->name,
            'url' => '<a href="http://' . $website->url . '">' . $website->url . '</a>',
            'type' => $website->type->description,
            'added_at' => $website->created_at->format('d/m/Y'),
            'status' => $website->status->description,
            'buttons' => [
                [
                    'link' => route('websites.snippet.javascript', ['website' => $website], false),
                    'label' => __('ui.pages.websites.index.view_javascript_snippet'),
                ],
            ],
            'control' => '',
        ];

        if (auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA)) {
            if (!$website->type->is(WebsiteType::PRIMARY)) {
                if ($website->trashed()) {
                    $data['buttons'][] = [
                        'link' => route('admin.publicAdministration.websites.restore', ['publicAdministration' => request()->route('publicAdministration'), 'website' => $website], false),
                        'label' => __('ui.pages.websites.index.restore_website'),
                        'dataAttributes' => [
                            'type' => 'deleteWebsiteStatus',
                        ],
                    ];
                } else {
                    $data['buttons'][] = [
                        'link' => route('admin.publicAdministration.websites.delete', ['publicAdministration' => request()->route('publicAdministration'), 'website' => $website], false),
                        'label' => __('ui.pages.websites.index.delete_website'),
                        'dataAttributes' => [
                            'type' => 'deleteWebsiteStatus',
                        ],
                    ];
                }
            }
        } else {
            if (!$website->status->is(WebsiteStatus::PENDING)) {
                $data['buttons'][] = [
                    'link' => route('analytics-service-login', [], false),
                    'label' => __('ui.pages.websites.index.go_to_analytics_service'),
                ];
            }

            if ($website->status->is(WebsiteStatus::PENDING) && auth()->user()->can(UserPermission::READ_ANALYTICS, $website)) {
                $data['buttons'][] = [
                    'link' => route('websites.tracking.check', ['website' => $website->slug], false),
                    'label' => __('ui.pages.websites.index.check_tracking'),
                    'dataAttributes' => [
                        'type' => 'checkTracking',
                    ],
                ];
            }

            if (auth()->user()->can(UserPermission::MANAGE_WEBSITES)) {
                if (!$website->type->is(WebsiteType::PRIMARY)) {
                    if ($website->status->is(WebsiteStatus::ACTIVE)) {
                        $data['buttons'][] = [
                            'link' => route('website.archive', ['website' => $website], false),
                            'label' => __('ui.pages.websites.index.archive'),
                            'dataAttributes' => [
                                'type' => 'archiveStatus',
                            ],
                        ];
                    } elseif ($website->status->is(WebsiteStatus::ARCHIVED)) {
                        $data['buttons'][] = [
                            'link' => route('website.unarchive', ['website' => $website], false),
                            'label' => __('ui.pages.websites.index.enable'),
                            'dataAttributes' => [
                                'type' => 'archiveStatus',
                            ],
                        ];
                    }
                }

                $data['buttons'][] = [
                    'link' => route('websites.show', ['website' => $website], false),
                    'label' => __('ui.pages.websites.index.show_website'),
                ];

                $data['buttons'][] = [
                    'link' => route('websites.edit', ['website' => $website], false),
                    'label' => __('ui.pages.websites.index.edit_website'),
                ];
            }
        }

        return $data;
    }
}
