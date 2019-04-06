<?php

namespace App\Transformers;

use App\Enums\WebsiteStatus;
use App\Enums\WebsiteType;
use App\Models\Website;
use League\Fractal\TransformerAbstract;

class WebsiteTransformer extends TransformerAbstract
{
    /**
     * @param \App\Models\Website $website
     *
     * @return array
     */
    public function transform(Website $website)
    {
        $last_month_visit = (int) app()->make('analytics-service')->getSiteLastMonthVisits($website->analytics_id);

        $data = [
            'url' => '<a href="http://' . $website->url . '">' . $website->url . '</a>',
            'type' => $website->type->description,
            'added_at' => $website->created_at->format('d/m/Y'),
            'status' => $website->status->description,
            'last_month_visits' => $last_month_visit,
            'actions' => [
                [
                    'link' => route('website-javascript-snippet', ['website' => $website], false),
                    'label' => __('ui.pages.websites.index.view_javascript_snippet'),
                ],
            ],
            'control' => '',
        ];

        if (!$website->status->is(WebsiteStatus::PENDING)) {
            $data['actions'][] = [
                'link' => route('analytics-service-login', [], false),
                'label' => __('ui.pages.websites.index.go_to_analytics_service'),
            ];
        }

        if (($website->status->is(WebsiteStatus::PENDING) || auth()->user()->can('manage-sites')) && !$website->type->is(WebsiteType::PRIMARY)) {
            $data['actions'][] = [
                'link' => route('websites-edit', ['website' => $website], false),
                'label' => __('ui.pages.websites.index.edit_website'),
            ];
        }

        return $data;
    }
}
