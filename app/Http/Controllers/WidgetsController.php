<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Enums\WebsiteStatus;
use App\Models\PublicAdministration;
use App\Models\Website;
use App\Traits\HasRoleAwareUrls;
use Illuminate\View\View;
use Symfony\Component\Yaml\Yaml;

class WidgetsController extends Controller
{
    use HasRoleAwareUrls;

    /**
     * Default constructor.
     */
    public function __construct()
    {
        $this->analyticsService = app()->make('analytics-service');
    }

    /**
     * Show the available widgets.
     *
     * @param Website $website The website
     * @param PublicAdministration $publicAdministration The PublicAdministration
     *
     * @return View The view
     */
    public function index(Website $website, PublicAdministration $publicAdministration): View
    {
        $publicAdministration = auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA)
            ? $publicAdministration
            : current_public_administration();

        if ($website->status->is(WebsiteStatus::PENDING)) {
            $redirectUrl = $this->getRoleAwareUrl('websites.show', ['website' => $website], $publicAdministration);
            $name = $website->name;

            return redirect()->to($redirectUrl)->withNotification([
                'title' => __('Errore'),
                'message' => __("Attiva il sito '$name' prima di accedere all'anteprima dei widget."),
                'status' => 'error',
                'icon' => 'it-close-circle',
            ]);
        }

        $analyticsId = $website->analytics_id;
        $widgetData = $this->analyticsService->getWidgetMetadata($analyticsId);
        $apiPublicDomain = config('analytics-service.api_public_domain');
        $widgetsBaseUrl = config('analytics-service.widgets_base_url');
        $allowedWidgets = Yaml::parseFile(resource_path('data/widgets.yml'));
        $allowedFqdns = $this->analyticsService->getSiteUrlsFromId($analyticsId);

        $data = [
            'widgets' => $widgetData,
            'idSite' => $analyticsId,
            'apiPublicDomain' => $apiPublicDomain,
            'widgetsBaseUrl' => $widgetsBaseUrl,
            'allowedWidgets' => $allowedWidgets['allowed_widgets_preview'] ?? [],
            'allowedFqdns' => $allowedFqdns,
        ];

        return view('pages.widgets-preview')->with($data);
    }
}
