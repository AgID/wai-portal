<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Jobs\SendVerificationEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminVerificationController extends Controller
{
    /**
     * Display token verification page.
     *
     * @return \Illuminate\Http\Response
     */
    public function verify()
    {
        if (auth()->check() && !auth()->user()->status->is(UserStatus::INVITED)) {
            return redirect()->home()->withMessage(['info' => "L'indirizzo email è già stato verificato."]); //TODO: put message in lang file
        }

        return view('auth.admin_verify');
    }

    /**
     * Perform token verification.
     *
     * @param Request $request
     * @param $token
     *
     * @return \Illuminate\Http\Response
     */
    public function verifyToken(Request $request, $email = null, $token = null)
    {
        $email = $email ?: $request->input('email');
        $token = $token ?: $request->input('token');

        validator(['email' => $email, 'token' => $token], [
            'email' => 'required|email',
            'token' => 'required|string',
        ])->validate();

        $user = User::where('email', $email)->first();

        if (empty($user)) {
            return redirect()->route('admin-verify')->withMessage(['warning' => "L'indirizzo email inserito non corrisponde ad un'utenza oppure il codice è errato."]); //TODO: put message in lang file
        }

        if (!$user->status->is(UserStatus::INVITED)) {
            return redirect()->route('admin-dashboard')
                ->withMessage(['info' => "L'indirizzo email è già stato verificato"]); //TODO: put message in lang file
        }

        if (!Hash::check($token, $user->verificationToken->token)) {
            return redirect()->route('admin-verify')->withMessage(['warning' => "L'indirizzo email inserito non corrisponde ad un'utenza oppure il codice è errato."]); //TODO: put message in lang file
        }

        $user->status = UserStatus::ACTIVE;
        $user->save();

        logger()->info('User ' . $user->uuid . ' confirmed email address.'); //TODO: notify me!

        if (!auth()->check()) {
            auth()->login($user);
            logger()->info('User ' . $user->uuid . ' logged in after email address verification.');
        }

        return redirect()->route('admin-password_change')
               ->withMessage(['success' => "L'indirizzo email è stato verificato correttamente."]); //TODO: put message in lang file
    }

    /**
     * Show resend confirmation email view.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function showResendForm()
    {
        if (auth()->check() && !auth()->user()->status->is(UserStatus::INVITED)) {
            return redirect()->home()->withMessage(['info' => "L'indirizzo email è già stato verificato."]); //TODO: put message in lang file
        }

        return view('auth.admin_resend');
    }

    /**
     * Resend confirmation email.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (empty($user) || !$user->status - is(UserStatus::INVITED)) {
            return redirect()->route('home')->withMessage(['info' => "Se l'indirizzo email inserito corrisponde ad un'utenza amministrativa, riceverai e breve un messaggio con un nuovo codice di verifica."]); //TODO: put message in lang file
        }

        if (!empty($user->verificationToken)) {
            $user->verificationToken->delete();
        }

        $token = hash_hmac('sha256', Str::random(40), config('app.key'));
        $user->verificationToken()->create([
            'token' => Hash::make($token),
        ]);

        dispatch(new SendVerificationEmail($user, $token));

        return redirect()->home()
               ->withMessage(['info' => "Se l'indirizzo email inserito corrisponde ad un'utenza amministrativa, riceverai e breve un messaggio con un nuovo codice di verifica."]); //TODO: put message in lang file
    }
}
