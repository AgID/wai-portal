<?php

namespace App\Transformers;

use App\Models\Website;
use League\Fractal\TransformerAbstract;

class WebsiteTransformer extends TransformerAbstract
{
    /**
    * @param \App\Models\Website $website
    * @return array
    */
    public function transform(Website $website)
    {
        $data = [
            'url' => '<a href="http://'.$website->url.'">'.$website->url.'</a>',
            'type' => __('ui.website.'.$website->type),
            'added_at' => $website->created_at->format('d/m/Y'),
            'status' => __('ui.website.'.$website->status),
            'last_month_visits' => $website->getLastMonthVisits(),
            'actions' => [
                [
                    'link' => route('website-javascript-snippet', ['website' => $website], false),
                    'label' => __('ui.pages.websites.index.view_javascript_snippet')
                ]
            ],
            'control' => ''
        ];

        if ($website->status != 'pending') {
            $data['actions'][] = [
                'link' => route('analytics-service-login', [], false),
                'label' => __('ui.pages.websites.index.go_to_analytics_service')
            ];
        }

        if (($website->status == 'pending' || auth()->user()->can('manage-sites')) && $website->type != 'primary') {
            $data['actions'][] = [
                'link' => route('websites-edit', ['website' => $website], false),
                'label' => __('ui.pages.websites.index.edit_website')
            ];
        }

        return $data;
    }
}
