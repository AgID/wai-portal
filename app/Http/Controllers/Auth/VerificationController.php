<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendVerificationEmail;
use App\Models\VerificationToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VerificationController extends Controller
{
    /**
     * Display token verification page
     *
     * @return \Illuminate\Http\Response
     */
    public function verify()
    {
        if (!auth()->check()) {
            return redirect()->guest(route('auth-register'))
                             ->withMessage(['warning' => "Prima di usare l'applicazione è necessario completare la registrazione"]); //TODO: put message in lang file
        } else {
            if (!in_array(auth()->user()->status, ['inactive', 'invited'])) {
                return redirect(route('home'))->withMessage(['info' => "L'indirizzo email è già stato verificato"]); //TODO: put message in lang file
            }
        }

    	return view('auth.verify');
    }

    /**
     * Perform token verification
     *
     * @param Request $request
     * @param $token
     * @return \Illuminate\Http\Response
     */
    public function verifyToken(Request $request, $token = null)
    {
        $token = $token ?: $request->input('token');
        $user = auth()->user();
        $verificationToken = $user->verificationToken->token;

        if (!Hash::check($token, $verificationToken)) {
            return redirect()->route('auth-verify')->withMessage(['warning' => "Il codice di verifica inserito non è valido per l'utente " . $user->name . ' ' . $user->familyName .'.']); //TODO: put message in lang file
        }

        if (!in_array($user->status, ['inactive', 'invited'])) {
            return redirect(route('home'))
                   ->withMessage(['info' => "L'indirizzo email è già stato verificato"]); //TODO: put message in lang file
        }

        if ($user->status == 'invited') {
            $SPIDUser = session()->get('spid_user');

            if ($user->fiscalNumber != $SPIDUser->fiscalNumber) {
                $request->session()->flash('message', ['warning' => "Il codice fiscale dell'utenza SPID è diverso da quello dell'invito."]);
                app()->make('SPIDAuth')->logout();
            }

            $user->fill([
                'spidCode' => $SPIDUser->spidCode,
                'name' => $SPIDUser->name,
                'familyName' => $SPIDUser->familyName,
                'status' => 'active',
                'analytics_password' => str_random(20)
            ]);

            $analyticsService = app()->make('analytics-service');

            $analyticsService->registerUser($user->email, $user->analytics_password, $user->email);

            $access = $user->can('manage-analytics') ? 'admin' : 'view';
            foreach ($user->getWebsites() as $website) {
                $analyticsService->setWebsitesAccess($user->email, $access, $website->analytics_id);
            }
        } else {
            $user->status = 'pending';
            $user->assign('registered');
        }

        $user->save();

        logger()->info('User '.$user->getInfo().' confirmed email address.'); //TODO: notify me!

        if (!auth()->check()) {
            auth()->login($user);
            logger()->info('User '.$user->getInfo().' logged in.');
        }

    	return redirect(route('home'))
               ->withMessage(['success' => "L'indirizzo email è stato verificato correttamente."]); //TODO: put message in lang file
    }

    /**
     * Resend confirmation email
     *
     * @return \Illuminate\Http\Response
     */
    public function resend()
    {
        if (!auth()->check()) {
            return redirect()->guest(route('auth-register'))
                             ->withMessage(['warning' => "Prima di usare l'applicazione è necessario completare la registrazione"]); //TODO: put message in lang file
        } else {
            $user = auth()->user();
            if (!in_array($user->status, ['inactive', 'invited'])) {
                return redirect(route('home'))->withMessage(['info' => "L'indirizzo email è già stato verificato"]); //TODO: put message in lang file
            }

            if (!empty($user->verificationToken)) {
                $user->verificationToken->delete();
            }

            $token = hash_hmac('sha256', str_random(40), config('app.key'));
            $user->verificationToken()->create([
                'token' => Hash::make($token)
            ]);

            dispatch(new SendVerificationEmail($user, $token));

            return redirect(route('home'))
                   ->withMessage(['info' => "Una nuova email di verifica è stata inviata all'indirizzo ".$user->email]); //TODO: put message in lang file
        }
    }
}
