<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SuperAdminDashboardController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return View
     */
    public function dashboard(): View
    {
        return view('pages.admin.dashboard');
    }
}
