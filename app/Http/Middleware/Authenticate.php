<?php

namespace App\Http\Middleware;

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
     * and is not suspended or deleted.
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
        if ($SPIDUser && User::findTrashedNotSuperAdminByFiscalNumber($SPIDUser->fiscalNumber)) {
            throw new AuthenticationException('User deleted.', $guards, $this->redirectTrashedTo($request));
        }

        parent::authenticate($request, $guards);
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
        return route('auth.register.show');
    }

    protected function redirectTrashedTo($request): string
    {
        $request->session()->flash('notification', [
            'title' => __('accesso negato'),
            'message' => __("L'utenza è stata rimossa."),
            'status' => 'error',
            'icon' => 'it-close-circle',
        ]);

        return route('home');
    }
}
