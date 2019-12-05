<?php

namespace App\Http\Controllers;

use App\Enums\UserPermission;
use App\Models\PublicAdministration;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * Show the application analytics dashboard or redirect to websites index page.
     *
     * @param Request $request the incoming request
     * @param PublicAdministration|null $publicAdministration the public administration the analytics data belong to
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request, PublicAdministration $publicAdministration = null)
    {
        $user = $request->user();
        if ($user->publicAdministrations->isEmpty() && $user->cannot(UserPermission::ACCESS_ADMIN_AREA)) {
            $request->session()->reflash();

            return redirect()->route('websites.index');
        }

        return view('pages.analytics')->with(['publicAdministration' => $publicAdministration]);
    }
}
