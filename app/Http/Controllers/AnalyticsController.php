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
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if ($user->publicAdministrations->isEmpty() && $user->cannot(UserPermission::ACCESS_ADMIN_AREA)) {
            $request->session()->reflash();

            return redirect()->route('websites.index');
        }

        $publicAdministration = !empty(request()->route('publicAdministration')) ? PublicAdministration::findByIpaCode(request()->route('publicAdministration')) : null;

        return view('pages.analytics')->with(['publicAdministration' => $publicAdministration]);
    }
}
