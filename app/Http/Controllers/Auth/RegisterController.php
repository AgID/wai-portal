<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\View\View;
use Ramsey\Uuid\Uuid;

/**
 * User registration controller.
 */
class RegisterController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View the view
     */
    public function showRegistrationForm(): View
    {
        return view('auth.register');
    }

    /**
     * Perform registration and login.
     *
     * @param Request $request the incoming request
     *
     * @throws \Exception if unable to generate user UUID
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'required|unique:users|email',
            'accept_terms' => 'required',
        ]);

        $SPIDUser = session()->get('spid_user');
        $user = User::create([
            'spidCode' => $SPIDUser->spidCode,
            'name' => $SPIDUser->name,
            'family_name' => $SPIDUser->familyName,
            'fiscal_number' => $SPIDUser->fiscalNumber,
            'uuid' => Uuid::uuid4()->toString(),
            'email' => $request->email,
            'status' => UserStatus::INACTIVE,
            'last_access_at' => Date::now(),
        ]);

        event(new Registered($user));

        $user->assign(UserRole::REGISTERED);
        auth()->login($user);

        return redirect()->home()
               ->withMessage(['info' => "Una email di verifica è stata inviata all'indirizzo " . $user->email]); //TODO: put message in lang file
    }
}
