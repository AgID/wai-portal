<?php

namespace App\Http\Middleware;

use App\Enums\UserPermission;
use Closure;
use Illuminate\Auth\Access\AuthorizationException;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $notSpid
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ?string $notSpid = null)
    {
        if ('notspid' === $notSpid && app()->make('SPIDAuth')->isAuthenticated()) {
            throw new AuthorizationException('SPID authenticated users are not authorized for route ' . $request->route()->getName() . '.');
        }

        if (auth()->check()) {
            $redirectTo = $request->user()->can(UserPermission::ACCESS_ADMIN_AREA) ? '/admin/dashboard' : '/dashboard';

            return redirect($redirectTo);
        }

        return $next($request);
    }
}
