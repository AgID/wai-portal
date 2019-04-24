<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Show the profile page.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function showProfile(Request $request)
    {
        return view('auth.profile.show')->with(['user' => $request->user()]);
    }

    /**
     * Show the profile form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showProfileForm(Request $request)
    {
        return view('auth.profile.edit')->with(['user' => $request->user()]);
    }

    /**
     * Update the specified user profile.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\User $user
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|unique:users|email',
        ]);

        $user = $request->user();
        $user->email = $validatedData['email'];
        $user->save();

        $redirectRoute = $user->isA(UserRole::SUPER_ADMIN) ? 'admin.profile' : 'user.profile';

        return redirect()->route($redirectRoute)
            ->withMessage(['success' => "L'indirizzo email è stato modificato correttamente."]); //TODO: put message in lang file
    }
}
