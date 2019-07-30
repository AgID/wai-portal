<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Models\PublicAdministration;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (auth()->user()->publicAdministrations->isEmpty() && auth()->user()->cannot(UserPermission::ACCESS_ADMIN_AREA)) {
            return redirect()->route('websites.create.primary');
        }

        $publicAdministration = !empty(request()->route('publicAdministration')) ? PublicAdministration::findByIPACode(request()->route('publicAdministration')) : null;

        return view('pages.dashboard')->with(['publicAdministration' => $publicAdministration]);
    }
}
