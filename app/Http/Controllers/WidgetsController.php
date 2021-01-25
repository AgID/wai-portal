<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Symfony\Component\Yaml\Yaml;

class WidgetsController extends Controller
{
    public function index(Website $website)
    {
        $analyticsId = $website->analytics_id;
        $widgetData = app()->make('analytics-service')->getWidgetMetadata($analyticsId);
        $matomoWidgetUrl = env('MATOMO_WIDGETS_URL');
        $allowedWidgets = Yaml::parseFile(resource_path('data/widgets.yml'));

        $data = [
            'widgets' => $widgetData,
            'idSite' => $analyticsId,
            'url' => $matomoWidgetUrl,
            'allowedWidgets' => $allowedWidgets["allowed_widgets_preview"] ?? []
        ];

        return view('pages.widgets-preview')->with($data);
    }
}
