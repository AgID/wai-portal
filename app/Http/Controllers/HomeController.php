<?php

namespace App\Http\Controllers;

use App\Models\PublicAdministration;
use App\Models\Website;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Symfony\Component\Yaml\Yaml;

class HomeController extends Controller
{
    /**
     * Show the application home.
     *
     * @return View the view
     */
    public function home(): View
    {
        $allWidgets = Yaml::parseFile(resource_path('data/widgets.yml'));
        $publicAdministrationsCount = PublicAdministration::getCount();
        $websitesCount = Website::getCount();

        $locale = app()->getLocale();

        $widgets = [];
        if (config('analytics-service.public_dashboard')) {
            $widgets = $allWidgets['public'] ?? [];
        }

        return view('pages.home')->with(compact('publicAdministrationsCount', 'websitesCount', 'widgets', 'locale'));
    }

    /**
     * Show the application faqs.
     *
     * @return View the view
     */
    public function faq(): View
    {
        $allFaqs = Yaml::parseFile(resource_path('data/faqs.yml'));
        $currentLocale = app()->getLocale();
        $faqsLocale = array_key_exists($currentLocale, $allFaqs) ? $currentLocale : config('app.fallback_locale');
        $faqs = $allFaqs[$faqsLocale];
        $themes = array_unique(Arr::flatten(array_map(function ($themes) {
            return explode(' ', $themes);
        }, Arr::pluck($faqs, 'themes'))));

        return view('pages.faq')->with(compact('faqs', 'themes'));
    }

    /**
     * Show the application contacts page.
     *
     * @return View the view
     */
    public function contacts(): View
    {
        return view('pages.contacts');
    }

    /**
     * Show the application open data page.
     *
     * @return View the view
     */
    public function openData(): View
    {
        return view('pages.open-data');
    }

    /**
     * Show the application privacy info.
     *
     * @return View the view
     */
    public function privacy(): View
    {
        return view('pages.privacy');
    }

    /**
     * Show the application legal notes.
     *
     * @return View the view
     */
    public function legalNotes(): View
    {
        return view('pages.legal_notes');
    }
}
