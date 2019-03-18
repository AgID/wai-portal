<?php

namespace App\Http\Controllers;

use Symfony\Component\Yaml\Yaml;

class HomeController extends Controller
{
    /**
     * Show the application home.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        return view('pages.home');
    }

    /**
     * Show the application faqs.
     *
     * @return \Illuminate\Http\Response
     */
    public function faq()
    {
        $faqs = Yaml::parseFile(resource_path('views/pages/faqs.yml'));

        return view('pages.faq')->with('faqs', $faqs);
    }

    /**
     * Show the application privacy info.
     *
     * @return \Illuminate\Http\Response
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * Show the application legal notes.
     *
     * @return \Illuminate\Http\Response
     */
    public function legalNotes()
    {
        return view('pages.legal_notes');
    }
}
