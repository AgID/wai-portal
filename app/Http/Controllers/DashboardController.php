<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (empty(auth()->user()->getWebsites())) {
            return redirect(route('add-primary-website'));
        }

        return view('pages.dashboard');
    }

    public function addPrimaryWebsite()
    {
        if (!empty(auth()->user()->getWebsites())) {
            return redirect(route('dashboard'));
        }
        return view('pages.add_primary_website');
    }
}
