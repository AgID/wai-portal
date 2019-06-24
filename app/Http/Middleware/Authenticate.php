<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

/**
 * User authentication.
 */
class Authenticate extends Middleware
{
    /**
     * Determine if the user is logged in to any of the given guards
     * and is not suspended.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $guards
     *
     * @throws \Illuminate\Auth\AuthenticationException
     *
     * @return void
     */
    protected function authenticate($request, array $guards)
    {
        $SPIDUser = session()->get('spid_user');
        if ($SPIDUser && User::findTrashedByFiscalNumber($SPIDUser->fiscalNumber)) {
            throw new AuthenticationException(
                'Deleted.', $guards, $this->redirectTrashedTo($request)
            );
        }

        parent::authenticate($request, $guards);

        if ($request->user()->status->is(UserStatus::SUSPENDED)) {
            throw new AuthenticationException(
                'Suspended.', $guards, $this->redirectSuspendedTo($request)
            );
        }
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function redirectTo($request): string
    {
        $request->session()->flash('message', [
            'warning' => "Prima di usare l'applicazione è necessario completare la registrazione",
        ]);

        return route('auth-register');
    }

    /**
     * Get the path the user should be redirected to when they are suspended.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return string
     */
    protected function redirectSuspendedTo($request): string
    {
        $request->session()->flash('message', [
            'error' => "L'utenza è stata sospesa.",
        ]);

        return route('home');
    }

    protected function redirectTrashedTo($request): string
    {
        $request->session()->flash('message', [
            'error' => "L'utenza è stata rimossa.",
        ]);

        return route('home');
    }
}
