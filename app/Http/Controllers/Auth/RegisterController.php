<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendVerificationEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
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

        $token = hash_hmac('sha256', Str::random(40), config('app.key'));
        $user->verificationToken()->create([
            'token' => Hash::make($token),
        ]);

        $user->assign('registered');
        auth()->login($user);

        logger()->info('New user registered: ' . $user->getInfo()); //TODO: notify me!

        dispatch(new SendVerificationEmail($user, $token));

        return redirect()->home()
               ->withMessage(['info' => "Una email di verifica è stata inviata all'indirizzo " . $user->email]); //TODO: put message in lang file
    }
}
