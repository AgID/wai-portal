<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Models\PublicAdministration;
use Illuminate\Http\Request;
use Symfony\Component\Yaml\Yaml;

/**
 * Public Administration analytics dashboard controller.
 */
class AnalyticsController extends Controller
{
    /**
     * Show the application analytics dashboard or redirect to websites index page.
     *
     * @param Request $request the incoming request
     * @param PublicAdministration|null $publicAdministration the public administration the analytics data belong to
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request, PublicAdministration $publicAdministration = null)
    {
        $user = $request->user();
        if ($user->publicAdministrations->isEmpty() && $user->cannot(UserPermission::ACCESS_ADMIN_AREA)) {
            $request->session()->reflash();

            return redirect()->route('websites.index');
        }

        if ($user->cannot(UserPermission::ACCESS_ADMIN_AREA)) {
            $publicAdministration = current_public_administration();
        }

        $locale = app()->getLocale();

        if ($publicAdministration->hasRollUp()) {
            $allWidgets = Yaml::parseFile(resource_path('data/widgets.yml'));
            $locale = array_key_exists($locale, $allWidgets) ? $locale : config('app.fallback_locale');
            $widgets = $allWidgets[$locale]['pa'] ?? [];
        }

        return view('pages.analytics')->with(['publicAdministration' => $publicAdministration, 'widgets' => $widgets ?? [], 'locale' => $locale]);
    }
}
