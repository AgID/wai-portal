<?php

namespace App\Http\Middleware;

use App\Enums\UserPermission;
use Closure;

/**
 * Guest users middleware.
 */
class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request the request
     * @param \Closure $next the next closure
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            $redirectTo = $request->user()->can(UserPermission::ACCESS_ADMIN_AREA) ? route('admin.dashboard') : route('analytics');

            return redirect($redirectTo);
        }

        return $next($request);
    }
}
