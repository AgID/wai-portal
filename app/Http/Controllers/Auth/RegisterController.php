<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Jobs\SendVerificationEmail;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Italia\SPIDAuth\SPIDUser;

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
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|unique:users|email'
        ]);
        $SPIDUser = session()->get('spid_user');
        $user =  User::create([
            'spidCode' => $SPIDUser->spidCode,
            'name' => $SPIDUser->name,
            'familyName' => $SPIDUser->familyName,
            'fiscalNumber' => $SPIDUser->fiscalNumber,
            'email' => $request->email,
            'status' => 'inactive'
        ]);
        $user->verificationToken()->create([
            'token' => bin2hex(random_bytes(32))
        ]);
        $user->assign('registered');
        auth()->login($user);

        logger()->info('New user registered: '.$user->getInfo()); //TODO: notify me!

        dispatch(new SendVerificationEmail($user));

        return redirect(route('home'))
               ->withMessage(['info' => "Una email di verifica è stata inviata all'indirizzo ".$user->email]); //TODO: put message in lang file
    }
}
