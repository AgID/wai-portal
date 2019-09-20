<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserRole;
use App\Exceptions\CommandErrorException;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * User profile controller.
 */
class ProfileController extends Controller
{
    /**
     * Show the profile form.
     *
     * @param Request $request the incoming request
     *
     * @return \Illuminate\View\View the view
     */
    public function edit(Request $request): View
    {
        return view('auth.profile')->with(['user' => $request->user()]);
    }

    /**
     * Update the specified user profile.
     *
     * @param Request $request the incoming request
     *
     * @throws CommandErrorException if command is unsuccessful
     *
     * @return \Illuminate\Http\RedirectResponse the server redirect response
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        $validator = validator($request->all(), [
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'name' => [
                'required',
                'string',
            ],
            'family_name' => [
                'required',
                'string',
            ],
        ]);

        $validator->after(function ($validator) use ($user, $request) {
            if ($user->email === $request->input('email')) {
                $validator->errors()->add('email', __('Il nuovo indirizzo email non può essere uguale a quello attuale.'));
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $validatedData = $validator->validated();

        // NOTE: the 'user update' event listener automatically
        //       sends a new email verification request and
        //       reset the email verification status
        $user->email = $validatedData['email'];
        if ($user->isA(UserRole::SUPER_ADMIN)) {
            $user->name = $validatedData['name'];
            $user->family_name = $validatedData['family_name'];
        }
        $user->save();

        if ($user->hasAnalyticsServiceAccount()) {
            //NOTE: remove the try/catch if matomo is configured
            //      to not send email on user updates using API interface
            //      See: https://github.com/matomo-org/matomo/pull/14281
            try {
                // Update Analytics Service account if needed
                // NOTE: at this point, user must have an analytics account
                $user->updateAnalyticsServiceAccountEmail();
            } catch (CommandErrorException $exception) {
                if (!Str::contains($exception->getMessage(), 'Unable to send mail.')) {
                    throw $exception;
                }
            }
        }

        return redirect()->home()
            ->withNotification([
                'title' => __('modifica utente'),
                'message' => implode("\n", [
                    __("La modifica dell'utente è andata a buon fine."),
                    __("Se è stato modificato l'indirizzo email, riceverai un messaggio per effettuarne la verifica."),
                ]),
                'status' => 'success',
                'icon' => 'it-check-circle',
            ]);
    }
}
