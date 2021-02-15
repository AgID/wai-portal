<?php

namespace App\Http\Controllers;

use App\Models\Website;
use Symfony\Component\Yaml\Yaml;

class WidgetsController extends Controller
{
    public function __construct()
    {
        $this->analyticsService = app()->make('analytics-service');
    }

    public function index(Website $website)
    {
        $analyticsId = $website->analytics_id;
        $widgetData = $this->analyticsService->getWidgetMetadata($analyticsId);
        $matomoWidgetUrl = config('analytics-service.widgets_url');
        $allowedWidgets = Yaml::parseFile(resource_path('data/widgets.yml'));
        $allowedFqdns = $this->analyticsService->getSiteUrlsFromId($analyticsId);

        $data = [
            'widgets' => $widgetData,
            'idSite' => $analyticsId,
            'widgetsBaseUrl' => $matomoWidgetUrl,
            'allowedWidgets' => $allowedWidgets['allowed_widgets_preview'] ?? [],
            'allowedFqdns' => $allowedFqdns,
        ];

        return view('pages.widgets-preview')->with($data);
    }
}
