<?php

namespace App\Http\Middleware;

use Closure;

class AdminAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return redirect()->guest(route('admin-login'));
        } elseif (!$request->user()->can('access-admin-area')) {
            abort(403);
        } elseif (auth()->user()->status == 'suspended') {
            abort(403);
        } elseif (is_null(auth()->user()->password)
                    && !$request->routeIs('admin-password_change')
                    && !$request->routeIs('admin-do_password_change')) {
            return redirect()->route('admin-password_change');
        }
        return $next($request);
    }
}
