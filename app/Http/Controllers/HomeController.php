<?php

namespace App\Http\Controllers;

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
        return view('pages.home');
    }

    /**
     * Show the application faqs.
     *
     * @return View the view
     */
    public function faq(): View
    {
        $faqs = Yaml::parseFile(resource_path('views/pages/faqs.yml'));
        $themes = array_unique(Arr::pluck($faqs, 'theme'));

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
