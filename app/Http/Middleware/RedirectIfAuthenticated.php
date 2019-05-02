<?php

namespace App\Http\Middleware;

use App\Enums\UserPermission;
use Closure;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (auth()->guard($guard)->check()) {
            $redirectTo = $request->user()->can(UserPermission::ACCESS_ADMIN_AREA) ? '/admin/dashboard' : '/dashboard';

            return redirect($redirectTo);
        }

        return $next($request);
    }
}
