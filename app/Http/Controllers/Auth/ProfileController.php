<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $user = $request->user();
        $validator = validator($request->all(), [
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $validator->after(function ($validator) use ($user, $request) {
            if ($user->email === $request->input('email')) {
                $validator->errors()->add('email', 'Il nuovo indirizzo email non può essere uguale a quello attuale.'); //TODO: put error message in lang file
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        $user->email = $request->input('email');
        $user->save();

        $redirectRoute = $user->isA(UserRole::SUPER_ADMIN) ? 'admin.profile' : 'user.profile';

        return redirect()->route($redirectRoute)
            ->withMessage([
                'success' => "L'indirizzo email è stato modificato correttamente.",
                'info' => "Una nuova email di verifica è stata inviata all'indirizzo " . $user->email . '.',
            ]); //TODO: put message in lang file
    }
}
