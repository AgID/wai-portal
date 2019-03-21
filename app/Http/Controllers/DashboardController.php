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
        if (auth()->user()->publicAdministrations->isEmpty()) {
            return redirect()->route('websites-add-primary');
        }

        return view('pages.dashboard');
    }
}
