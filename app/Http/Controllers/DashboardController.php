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
            return redirect(route('websites-add-primary'));
        }

        return view('pages.dashboard');
    }
}
