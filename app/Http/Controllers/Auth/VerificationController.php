<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserPermission;
use App\Enums\UserRole;
use App\Enums\UserStatus;
use App\Events\User\UserActivated;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Email verification controller.
 */
class VerificationController extends Controller
{
    /**
     * Show the email verification notice.
     *
     * @param \Illuminate\Http\Request $request the incoming request
     *
     * @return mixed the view for verification notice or a redirect if user is already verified
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
     * @param \Illuminate\Http\Request $request the incoming request
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to SPID service
     * @throws \Illuminate\Auth\Access\AuthorizationException if verification link is invalid
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function verify(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (!$user) {
            throw new AuthorizationException();
        }

        if (!Hash::check($user->email, base64_decode($request->route('hash'), true)) || $request->route('uuid') !== $user->getAttribute($user->getRouteKeyName())) {
            throw new AuthorizationException(__("L'utente non corrisponde all'invito."));
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
     * @param \Illuminate\Http\Request $request the incoming request
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function resend(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->alreadyVerifiedUser($user);
        }

        $user->sendEmailVerificationNotification();

        return back()->withMessage(['info' => "Una nuova email di verifica è stata inviata all'indirizzo " . $user->email]); //TODO: put message in lang file;
    }

    /**
     * Redirect an already verified user.
     *
     * @param User $user the user
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    protected function alreadyVerifiedUser(User $user): RedirectResponse
    {
        return redirect()->home()
            ->withMessage(['info' => "L'indirizzo email dell'utente " . $user->getInfo() . ' è già stato verificato.']); //TODO: put message in lang file
    }

    /**
     * Update a verified user.
     *
     * @param User $user the user
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to SPID service
     *
     * @return bool true if user is successfully updated, false otherwise
     */
    protected function verifyUser(User $user): bool
    {
        if ($user->status->is(UserStatus::INACTIVE)) {
            $newStatus = UserStatus::PENDING;
            $user->assign(UserRole::REGISTERED);
        }

        if ($user->status->is(UserStatus::INVITED)) {
            $newStatus = UserStatus::ACTIVE;

            if ($user->isNotA(UserRole::SUPER_ADMIN)) {
                $SPIDUser = session()->get('spid_user');

                if ($user->fiscal_number !== $SPIDUser->fiscalNumber) {
                    session()->flash('message', ['error' => "L'utente non corrisponde all'invito."]);

                    return app()->make('SPIDAuth')->logout();
                }
                $user->fill([
                    'spidCode' => $SPIDUser->spidCode,
                    'name' => $SPIDUser->name,
                    'family_name' => $SPIDUser->familyName,
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
