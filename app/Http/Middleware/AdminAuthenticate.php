<?php

namespace App\Http\Middleware;

use App\Enums\UserStatus;
use Closure;

/**
 * Admin authenticattion middleware.
 */
class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request the incoming request
     * @param \Closure $next the next closure
     *
     * @return mixed the check result
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()) {
            return redirect()->guest(route('admin-login'));
        }

        if (!$request->user()->can('access-admin-area')) {
            abort(403);
        }

        if ($request->user()->status->is(UserStatus::SUSPENDED)) {
            abort(403, "L'utenza è stata sospesa."); // TODO: put in lang file
        }

        return $next($request);
    }
}
