<?php

namespace App\Http\Controllers;

use App\Models\PublicAdministration;

class AdminController extends Controller
{
    /**
     * Show the admin dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        return view('pages.admin.dashboard')->with(['publicAdministrations' => PublicAdministration::all(['ipa_code', 'name'])]);
    }
}
