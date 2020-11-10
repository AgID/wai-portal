<?php

namespace App\Http\Controllers;

use App\Models\PublicAdministration;
use App\Models\Website;
use App\Traits\BuildDatasetForSingleDigitalGatewayAPI;
use App\Traits\GetsLocalizedYamlContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Symfony\Component\Yaml\Yaml;

class HomeController extends Controller
{
    use GetsLocalizedYamlContent;
    use BuildDatasetForSingleDigitalGatewayAPI;

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
        $faqs = $this->getLocalizedYamlContent('faqs');
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
     * Show the "how to join" page.
     *
     * @return View the view
     */
    public function howToJoin(): View
    {
        $steps = $this->getLocalizedYamlContent('how-to-join-steps');

        return view('pages.how-to-join')->with(compact('steps'));
    }

    /**
     * Show the application open data page.
     *
     * @return View the view
     */
    // public function openData(): View
    // {
    //     return view('pages.open-data');
    // }

    /**
     * Show the dataset for SDG.
     *
     * @return JsonResponse
     */
    /* public function showSDGDataset(): JsonResponse
    {
        $data = $this->buildDatasetForSDG();

        return response()->json($data);
    } */

    /**
     * Show the application privacy info.
     *
     * @return View the view
     */
    public function privacy(): View
    {
        $privacy = $this->getLocalizedYamlContent('privacy');

        return view('pages.privacy')->with(compact('privacy'));
    }

    /**
     * Show the application legal notes.
     *
     * @return View the view
     */
    public function legalNotes(): View
    {
        $legal = $this->getLocalizedYamlContent('legal');

        return view('pages.legal_notes')->with(compact('legal'));
    }
}
