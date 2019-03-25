<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Perform registration and login.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|unique:users|email',
            'accept_terms' => 'required',
        ]);

        $SPIDUser = session()->get('spid_user');
        $user = User::create([
            'spidCode' => $SPIDUser->spidCode,
            'name' => $SPIDUser->name,
            'familyName' => $SPIDUser->familyName,
            'fiscalNumber' => $SPIDUser->fiscalNumber,
            'email' => $request->email,
            'status' => 'inactive',
        ]);

        event(new Registered($user));

        $user->assign('registered');
        auth()->login($user);

        return redirect()->home()
               ->withMessage(['info' => "Una email di verifica è stata inviata all'indirizzo " . $user->email]); //TODO: put message in lang file
    }
}
