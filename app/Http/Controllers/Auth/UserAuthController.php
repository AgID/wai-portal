<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

class UserAuthController extends Controller
{
    /**
     * Show the profile page.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        return view('auth.profile')->with(['user' => auth()->user()]);
    }
}
