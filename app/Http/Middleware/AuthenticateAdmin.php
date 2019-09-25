<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use App\Http\Middleware\Authenticate as AuthenticateMiddleware;
use Illuminate\Auth\Access\AuthorizationException;

/**
 * Admin authentication middleware.
 */
class AuthenticateAdmin extends AuthenticateMiddleware
{
    /**
     * Determine if the user is logged in to any of the given guards
     * and has a super admin role.
     *
     * @param \Illuminate\Http\Request $request
     * @param array $guards
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     *
     * @return void
     */
    protected function authenticate($request, array $guards)
    {
        parent::authenticate($request, $guards);

        if (!$request->user()->isA(UserRole::SUPER_ADMIN)) {
            throw new AuthorizationException('Current user in not a super administrator.');
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
        return route('admin.login.show');
    }
}
