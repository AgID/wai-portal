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
            return $this->alreadyVerifiedUser($request, $user);
        }

        return view('auth.verify')->with('user', $user);
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param \Illuminate\Http\Request $request the incoming request
     * @param string $uuid the uuid of the user to be verified
     *
     * @throws \Illuminate\Contracts\Container\BindingResolutionException if unable to bind to SPID service
     * @throws \Illuminate\Auth\Access\AuthorizationException if verification link is invalid
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function verify(Request $request, string $uuid): RedirectResponse
    {
        $user = User::where('uuid', $uuid)->first();

        if (!$user || !Hash::check($user->email, base64_decode($request->route('hash'), true))) {
            throw new AuthorizationException('Current user does not match invitation.');
        }

        if ($user->hasVerifiedEmail()) {
            return $this->alreadyVerifiedUser($request, $user);
        }

        if ($this->verifyUser($user)) {
            event(new Verified($user));
        }

        $dashboard = $user->can(UserPermission::ACCESS_ADMIN_AREA) ? '/admin/dashboard' : '/dashboard';

        return redirect($dashboard)->withModal([
            'title' => __('Indirizzo email confermato'),
            'icon' => 'it-check-circle',
            'message' => __("Hai appena confermato il tuo indirizzo email <strong>:email</strong>.\nDa adesso puoi iniziare a usare Web Analytics Italia.", ['email' => $user->email]),
        ]);
    }

    /**
     * Resend the email verification notification.
     *
     * @param \Illuminate\Http\Request $request the incoming request
     * @param User|null $user the user to resend the notification to
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse the response
     */
    public function resend(Request $request, User $user)
    {
        $user = $user->exists ? $user : $request->user();

        if ($user->hasVerifiedEmail()) {
            return $this->alreadyVerifiedUser($request, $user);
        }

        $user->sendEmailVerificationNotification($user->status->is(UserStatus::INVITED) ? $user->publicAdministrations()->first() : null);

        return $request->expectsJson()
            ? response()->json([
                'result' => 'ok',
                'id' => $user->uuid,
                'email' => $user->email,
            ])
            : back()->withNotification([
                'title' => __('verifica indirizzo email'),
                'message' => __("Una nuova email di verifica è stata inviata all'indirizzo <strong>:email</strong>.", ['email' => $user->email]),
                'status' => 'success',
                'icon' => 'it-check-circle',
            ]);
    }

    /**
     * Redirect an already verified user.
     *
     * @param User $user the user
     * @param Request $request the current request
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    protected function alreadyVerifiedUser(Request $request, User $user): RedirectResponse
    {
        return $request->expectsJson()
            ? response()->json(null, 304)
            : redirect()->home()->withNotification([
                'title' => __('verifica indirizzo email'),
                'message' => __("L'indirizzo email <strong>:email</strong> è già stato verificato dall'utente.", ['email' => $user->email]),
                'status' => 'warning',
                'icon' => 'it-warning-circle',
            ]);
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

            if ($user->isNotAn(UserRole::SUPER_ADMIN)) {
                $SPIDUser = session()->get('spid_user');

                if ($user->fiscal_number !== $SPIDUser->fiscalNumber) {
                    session()->flash('notification', [
                        'title' => __('accesso negato'),
                        'message' => __("L'utente non corrisponde all'invito."),
                        'status' => 'error',
                        'icon' => 'it-close-circle',
                    ]);

                    return app()->make('SPIDAuth')->logout();
                }
                $user->fill([
                    'spid_code' => $SPIDUser->spidCode,
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
