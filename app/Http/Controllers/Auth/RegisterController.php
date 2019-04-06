<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

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
            'uuid' => Uuid::uuid4()->toString(),
            'email' => $request->email,
            'status' => UserStatus::INACTIVE,
            'partial_analytics_password' => Str::random(rand(32, 48)),
        ]);

        event(new Registered($user));

        $user->assign('registered');
        auth()->login($user);

        //TODO: da gestire meglio con la CRUD utenti
        app()->make('analytics-service')->registerUser($user->uuid, $user->analytics_password, $user->email, config('analytics-service.admin_token'), $user->full_name);

        return redirect()->home()
               ->withMessage(['info' => "Una email di verifica è stata inviata all'indirizzo " . $user->email]); //TODO: put message in lang file
    }
}
