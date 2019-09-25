<?php

namespace App\Http\Controllers;

class SuperAdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        return view('pages.admin.dashboard');
    }
}
