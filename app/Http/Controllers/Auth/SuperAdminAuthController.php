<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserPermission;
use App\Enums\UserStatus;
use App\Events\User\UserLogin;
use App\Events\User\UserLogout;
use App\Http\Controllers\Controller;
use App\Jobs\ClearPasswordResetToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Super admin authentication controller.
 */
class SuperAdminAuthController extends Controller
{
    use ThrottlesLogins;

    /**
     * The maximum failed login attempt.
     *
     * @var int max login attempts
     */
    protected $maxAttempts = 3;

    /**
     * The failed attempt reset decay (in minutes).
     *
     * @var int the lockout minutes
     */
    protected $decayMinutes = 5;

    /**
     * Show the login form or redirect to admin dashboard.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View the server redirect response or the login view
     */
    public function showLogin()
    {
        if (auth()->check() && auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA)) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return view('auth.admin_login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws ValidationException
     *
     * @return mixed the server redirect response or a validation exception redirect
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email:rfc,dns|max:75',
            'password' => 'required|string|max:50',
        ]);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $intendedUrl = session('url.intended');
        session()->invalidate();
        if ($intendedUrl) {
            redirect()->setIntendedUrl($intendedUrl);
        }

        if (auth()->attempt($request->only('email', 'password'))) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return redirect()->route('admin.login.show')->withNotification([
            'title' => __('non autenticato'),
            'message' => __('auth.failed'),
            'status' => 'error',
            'icon' => 'it-close-circle',
        ])->withInput();
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function logout(): RedirectResponse
    {
        $user = auth()->user();
        if ($user && $user->can(UserPermission::ACCESS_ADMIN_AREA)) {
            auth()->logout();
            session()->invalidate();

            event(new UserLogout($user));
        }

        return redirect()->home();
    }

    /**
     * Display the password reset form.
     *
     * @return \Illuminate\View\View the view
     */
    public function showPasswordForgot(): View
    {
        return view('auth.admin_password_forgot');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param \Illuminate\Http\Request $request the incoming request
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function sendPasswordForgot(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email:rfc,dns|max:75']);
        $email = $request->input('email');

        $user = User::where('email', $email)->first();
        if (empty($user) || $user->cant(UserPermission::ACCESS_ADMIN_AREA) || !$user->status->is(UserStatus::ACTIVE)) {
            return redirect()->home()->withNotification([
                'title' => __('Reset della password'),
                'message' => __("Se l'indirizzo email inserito corrisponde ad un'utenza amministrativa registrata e attiva, riceverai e breve un messaggio con le istruzioni per il reset della password."),
                'status' => 'info',
                'icon' => 'it-info-circle',
            ]);
        }

        if (!empty($user->passwordResetToken)) {
            $user->passwordResetToken->delete();
        }

        $token = hash_hmac('sha256', Str::random(40), config('app.key'));
        $user->passwordResetToken()->create([
            'token' => Hash::make($token),
            'created_at' => now(),
        ]);

        $user->load('passwordResetToken');
        $user->sendPasswordResetRequestNotification($token);
        dispatch(new ClearPasswordResetToken($user->passwordResetToken))->delay(now()->addHour());

        return redirect()->home()->withNotification([
            'title' => __('Reset della password'),
            'message' => __("Se l'indirizzo email inserito corrisponde ad un'utenza amministrativa registrata e attiva, riceverai e breve un messaggio con le istruzioni per il reset della password."),
            'status' => 'info',
            'icon' => 'it-info-circle',
        ]);
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param \Illuminate\Http\Request $request the incoming request
     * @param string|null $token the reset token
     *
     * @return \Illuminate\View\View the view
     */
    public function showPasswordReset(Request $request, ?string $token = null): View
    {
        $token = $token ?? $request->input('token');

        return view('auth.admin_password_reset')->with(['token' => $token]);
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Http\Request $request the incoming request
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function passwordReset(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'token' => 'required',
            'email' => 'required|email:rfc,dns|max:75',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/',
                'max:50',
            ],
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (empty($user)) {
            return redirect()->route('admin.password.reset.show')->withNotification([
                'title' => __('errore nella richiesta'),
                'message' => __("L'indirizzo email inserito non corrisponde ad un'utenza oppure il codice è scaduto o errato."),
                'status' => 'warning',
                'icon' => 'it-error',
            ])->withInput();
        }

        if (empty($user->passwordResetToken) || !Hash::check($validatedData['token'], $user->passwordResetToken->token)) {
            return redirect()->route('admin.password.reset.show')->withNotification([
                'title' => __('errore nella richiesta'),
                'message' => __("L'indirizzo email inserito non corrisponde ad un'utenza oppure il codice è scaduto o errato."),
                'status' => 'warning',
                'icon' => 'it-error',
            ])->withInput();
        }

        $user->password = Hash::make($validatedData['password']);
        $user->password_changed_at = Carbon::now();
        $user->save();
        $user->passwordResetToken->delete();

        event(new PasswordReset($user));

        auth()->login($user);

        return redirect()->route('admin.dashboard')->withNotification([
            'title' => __('Reset della password'),
            'message' => __('La password è stata reimpostata.'),
            'status' => 'success',
            'icon' => 'it-check-circle',
        ]);
    }

    /**
     * Display the password change view.
     *
     * @return \Illuminate\View\View the view
     */
    public function showPasswordChange(): View
    {
        return view('auth.admin_password_change');
    }

    /**
     * Change the user's password.
     *
     * @param \Illuminate\Http\Request $request the incoming request
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function passwordChange(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/',
                'max:50',
            ],
        ]);

        $user = auth()->user();

        $user->password = Hash::make($validatedData['password']);
        $user->password_changed_at = Carbon::now();
        $user->save();

        return redirect()->intended(route('admin.dashboard'))->withNotification([
            'title' => __('Reset della password'),
            'message' => __('La password è stata cambiata.'),
            'status' => 'success',
            'icon' => 'it-check-circle',
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string the username to use
     */
    public function username(): string
    {
        return 'email';
    }

    /**
     * Redirect the user after determining they are locked out.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return void
     */
    protected function sendLockoutResponse(Request $request)
    {
        throw new ThrottleRequestsException('Too Many Attempts.');
    }

    /**
     * Send the response after the user is authenticated.
     *
     * @param \Illuminate\Http\Request $request the incoming request
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    protected function sendLoginResponse(Request $request): RedirectResponse
    {
        $this->clearLoginAttempts($request);

        event(new UserLogin(auth()->user()));

        return redirect()->intended(route('admin.dashboard'));
    }
}
