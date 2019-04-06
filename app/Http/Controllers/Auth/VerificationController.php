<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VerificationController extends Controller
{
    /**
     * Show the email verification notice.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->alreadyVerifiedUser($user);
        }

        return view('auth.verify')->with('user', $user);
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return \Illuminate\Http\Response
     */
    public function verify(Request $request)
    {
        $user = $request->user();

        if (!$user || ($request->route('uuid') != $user->getAttribute($user->getRouteKeyName()))) {
            throw new AuthorizationException();
        }

        if ($user->hasVerifiedEmail()) {
            return $this->alreadyVerifiedUser($user);
        }

        if ($this->verifyUser($user)) {
            event(new Verified($user));
        }

        $dashboard = $request->user()->can('access-admin-area') ? '/admin/dashboard' : '/dashboard';

        return redirect($dashboard)
            ->withMessage(['success' => "L'indirizzo email è stato verificato correttamente."]); //TODO: put message in lang file
    }

    /**
     * Resend the email verification notification.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function resend(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->alreadyVerifiedUser($user);
        }

        $user->sendEmailVerificationNotification();

        return back()->withMessage(['info' => "Una nuova email di verifica è stata inviata all'indirizzo " . $user->email]); //TODO: put message in lang file;
    }

    /**
     * Redirect home already verified users.
     */
    protected function alreadyVerifiedUser($user)
    {
        return redirect()->home()
            ->withMessage(['info' => "L'indirizzo email dell'utente " . $user->getInfo() . ' è già stato verificato.']); //TODO: put message in lang file
    }

    /**
     * Update a verified user.
     */
    protected function verifyUser($user)
    {
        if ($user->status->is(UserStatus::INACTIVE)) {
            $newStatus = UserStatus::PENDING;
            $user->assign('registered');
        }

        if ($user->status->is(UserStatus::INVITED)) {
            $newStatus = UserStatus::ACTIVE;

            if (!$user->isA('super-admin')) {
                $SPIDUser = session()->get('spid_user');

                if ($user->fiscalNumber !== $SPIDUser->fiscalNumber) {
                    session()->flash('message', ['error' => "Il codice fiscale dell'utenza SPID è diverso da quello dell'invito."]);

                    return app()->make('SPIDAuth')->logout();
                }

                $user->fill([
                    'spidCode' => $SPIDUser->spidCode,
                    'name' => $SPIDUser->name,
                    'familyName' => $SPIDUser->familyName,
                    'partial_analytics_password' => Str::random(rand(32, 48)),
                ]);

                $newStatus = UserStatus::ACTIVE;

                // To be moved in user creation
                // $analyticsService = app()->make('analytics-service');

                // $analyticsService->registerUser($user->email, $user->analytics_password, $user->email);

                // $access = $user->can('manage-analytics') ? 'admin' : 'view';
                // foreach ($user->getWebsites() as $website) {
                //     $analyticsService->setWebsitesAccess($user->email, $access, $website->analytics_id);
                // }
            }
        }

        $user->status = $newStatus;
        $user->email_verified_at = $user->freshTimestamp();

        return $user->save();
    }
}
