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
        } elseif (!$request->user()->can('access-backoffice')) {
            abort(403);
        } elseif (auth()->user()->status == 'suspended') {
            abort(403);
        }
        return $next($request);
    }
}
