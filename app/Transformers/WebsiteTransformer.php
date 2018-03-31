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
                    'link' => route('website-javascript-snippet', ['website' => $website->slug], false),
                    'label' => __('ui.pages.websites.view_javascript_snippet')
                ]
            ],
            'control' => ''
        ];

        if ($website->status != 'pending') {
            $data['actions'][] = [
                'link' => route('analytics-service-login', [], false),
                'label' => __('ui.pages.websites.go_to_analytics_service')
            ];
        }

        return $data;
    }
}
