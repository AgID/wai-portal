<?php

namespace App\Transformers;

use App\Enums\UserPermission;
use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Models\Website;
use League\Fractal\TransformerAbstract;

class WebsiteTransformer extends TransformerAbstract
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
            'last_month_visits' => 'N/A',
            'buttons' => [
                [
                    'link' => route('websites.snippet.javascript', ['website' => $website], false),
                    'label' => __('ui.pages.websites.index.view_javascript_snippet'),
                ],
            ],
            'control' => '',
        ];

        if (!$website->status->is(WebsiteStatus::PENDING)) {
            $data['buttons'][] = [
                'link' => route('analytics-service-login', [], false),
                'label' => __('ui.pages.websites.index.go_to_analytics_service'),
            ];
        }

        if (!$website->status->is(WebsiteStatus::PENDING) && auth()->user()->can(UserPermission::READ_ANALYTICS, $website)) {
            $data['last_month_visits'] = (int) app()->make('analytics-service')->getSiteLastMonthVisits($website->analytics_id, current_user_auth_token());
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

        if (!$website->type->is(WebsiteType::PRIMARY) && auth()->user()->can(UserPermission::MANAGE_WEBSITES)) {
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

        if (($website->status->is(WebsiteStatus::PENDING) || auth()->user()->can(UserPermission::MANAGE_WEBSITES)) && !$website->type->is(WebsiteType::PRIMARY)) {
            $data['buttons'][] = [
                'link' => route('websites.edit', ['website' => $website], false),
                'label' => __('ui.pages.websites.index.edit_website'),
            ];
        }

        return $data;
    }
}
