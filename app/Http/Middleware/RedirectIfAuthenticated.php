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
     * @param string|null $notSpid
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            $redirectTo = $request->user()->can(UserPermission::ACCESS_ADMIN_AREA) ? route('admin.dashboard') : route('dashboard');

            return redirect($redirectTo);
        }

        return $next($request);
    }
}
