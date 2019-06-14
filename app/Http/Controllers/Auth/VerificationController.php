<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserActivated;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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

        if (!$user) {
            throw new AuthorizationException();
        }

        if (!Hash::check($user->email, base64_decode($request->route('hash'), true)) || $request->route('uuid') !== $user->getAttribute($user->getRouteKeyName())) {
            throw new AuthorizationException("L'utente non corrisponde all'invito."); //TODO: put message in lang file
        }

        if ($user->hasVerifiedEmail()) {
            return $this->alreadyVerifiedUser($user);
        }

        if ($this->verifyUser($user)) {
            event(new Verified($user));
        }

        $dashboard = $request->user()->can(UserPermission::ACCESS_ADMIN_AREA) ? '/admin/dashboard' : '/dashboard';

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

            if ($user->isNotA(UserRole::SUPER_ADMIN)) {
                $SPIDUser = session()->get('spid_user');

                if ($user->fiscalNumber !== $SPIDUser->fiscalNumber) {
                    session()->flash('message', ['error' => "L'utente non corrisponde all'invito."]);

                    return app()->make('SPIDAuth')->logout();
                }
                $user->fill([
                    'spidCode' => $SPIDUser->spidCode,
                    'name' => $SPIDUser->name,
                    'familyName' => $SPIDUser->familyName,
                ]);
                $newStatus = UserStatus::ACTIVE;

                event(new UserActivated($user, $user->publicAdministrations()->first()));
            }
        }

        $user->status = $newStatus ?? $user->status->value;
        $user->email_verified_at = $user->freshTimestamp();

        return $user->save();
    }
}
