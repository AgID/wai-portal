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
}
