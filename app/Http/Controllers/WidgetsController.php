<?php

namespace App\Http\Controllers;

use App\Models\Website;

class WidgetsController extends Controller
{
    public function index(Website $website)
    {
        $analyticsId = $website->analytics_id;
        $widgetData = app()->make('analytics-service')->getWidgetMetadata($analyticsId);
        $matomoWidgetUrl = env('MATOMO_WIDGETS_URL');

        $data = [
            'widgets' => $widgetData,
            'idSite' => $analyticsId,
            'url' => $matomoWidgetUrl,
        ];

        return view('pages.widgets-preview')->with($data);
    }
}
