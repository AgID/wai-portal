<?php

namespace App\Http\Middleware;

use Closure;

class SelectTenant
{
    /**
     * Check whether the session has a tenant selected for the current request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string|null $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (empty(session('tenant_id'))) {
            if ($request->user() && $request->user()->publicAdministrations->isNotEmpty()) {
                session()->put('tenant_id', $request->user()->publicAdministrations()->first()->id);
                // return redirect('/select-public-administration');
            }
        }

        return $next($request);
    }
}
