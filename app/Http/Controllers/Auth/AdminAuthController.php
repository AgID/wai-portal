<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserPermission;
use App\Enums\UserStatus;
use App\Events\User\UserLogin;
use App\Events\User\UserLogout;
use App\Http\Controllers\Controller;
use App\Jobs\ClearPasswordResetToken;
use App\Jobs\SendPasswordResetEmail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    use ThrottlesLogins;

    protected $maxAttempts = 3;
    protected $decayMinutes = 5;

    /**
     * Show the login form or redirect to admin dashboard.
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response|void
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
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

        return redirect()->route('admin.login.show')->withMessage(['error' => __('auth.failed')])->withInput();
    }

    /**
     * Log the user out of the application.
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        if (auth()->check() && auth()->user()->can(UserPermission::ACCESS_ADMIN_AREA)) {
            $user = auth()->user();
            auth()->logout();
            session()->invalidate();

            event(new UserLogout($user));
        }

        return redirect()->home();
    }

    /**
     * Display the password reset form.
     *
     * @return \Illuminate\View\View
     */
    public function showPasswordForgot(): View
    {
        return view('auth.admin_password_forgot');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendPasswordForgot(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->input('email');

        $user = User::where('email', $email)->first();
        if (empty($user) || $user->cant(UserPermission::ACCESS_ADMIN_AREA) || !$user->status->is(UserStatus::ACTIVE)) {
            return redirect()->home()->withMessage(['info' => "Se l'indirizzo email inserito corrisponde ad un'utenza amministrativa registrata e attiva, riceverai e breve un messaggio con le istruzioni per il reset della password."]);
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

        dispatch(new SendPasswordResetEmail($user, $token));
        dispatch(new ClearPasswordResetToken($user->passwordResetToken))->delay(now()->addHour());

        return redirect()->home()->withMessage(['info' => "Se l'indirizzo email inserito corrisponde ad un'utenza amministrativa registrata e attiva, riceverai e breve un messaggio con le istruzioni per il reset della password."]);
    }

    /**
     * Display the password reset view for the given token.
     *
     * @param \Illuminate\Http\Request $request
     * @param string|null $token
     *
     * @return \Illuminate\View\View
     */
    public function showPasswordReset(Request $request, $token = null): View
    {
        $token = $token ?: $request->input('token');

        return view('auth.admin_password_reset')->with(['token' => $token]);
    }

    /**
     * Reset the given user's password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function passwordReset(Request $request)
    {
        $validatedData = $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/',
            ],
        ]);

        $user = User::where('email', $validatedData['email'])->first();

        if (empty($user)) {
            return redirect()->route('admin.password.reset.show')->withMessage(['error' => "L'indirizzo email inserito non corrisponde ad un'utenza oppure il codice è scaduto o errato."])->withInput(); //TODO: put message in lang file
        }

        if (empty($user->passwordResetToken) || !Hash::check($validatedData['token'], $user->passwordResetToken->token)) {
            return redirect()->route('admin.password.reset.show')->withMessage(['error' => "L'indirizzo email inserito non corrisponde ad un'utenza oppure il codice è scaduto o errato."])->withInput(); //TODO: put message in lang file
        }

        $user->password = Hash::make($validatedData['password']);
        $user->password_changed_at = Carbon::now();
        $user->save();
        $user->passwordResetToken->delete();

        event(new PasswordReset($user));

        auth()->login($user);

        return redirect()->route('admin.dashboard')->withMessage(['success' => __('auth.password.reset')]);
    }

    /**
     * Display the password change view.
     *
     * @return \Illuminate\View\View
     */
    public function showPasswordChange(): View
    {
        return view('auth.admin_password_change');
    }

    /**
     * Change the user's password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function passwordChange(Request $request)
    {
        $validatedData = $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*(_|[^\w])).+$/',
            ],
        ]);

        $user = auth()->user();

        $user->password = Hash::make($validatedData['password']);
        $user->password_changed_at = Carbon::now();
        $user->save();

        return redirect()->intended(route('admin.dashboard'))->withMessage(['success' => __('auth.password.changed')]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        event(new UserLogin(auth()->user()));

        return redirect()->intended(route('admin.dashboard'));
    }
}
